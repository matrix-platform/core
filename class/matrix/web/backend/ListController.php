<?php //>

namespace matrix\web\backend;

use matrix\db\Criteria;

class ListController extends Controller {

    protected static $defaults = [
        'exportColumns' => 'columns',
    ];

    private $columns;
    private $export;

    public function __construct($name) {
        $this->values = [
            'defaultPage' => 1,
            'defaultPageSize' => 10,
            'defaultRanking' => true,
            'exportView' => 'backend/export.php',
            'modalForm' => cfg('backend.modal-form'),
            'sublistView' => 'backend/sublist.php',
            'table' => table($name),
            'view' => 'backend/list.php',
        ];
    }

    public function available() {
        $relation = $this->getParentRelation();

        if ($relation) {
            if ($this->method() === 'POST') {
                if ($relation['self-referencing']) {
                    $pattern = preg_quote($this->name(), '/');
                    $table = $this->table();
                    $relation = $table->getComposition($table);

                    return preg_match("/^{$pattern}(\/[\d-]+\/{$relation['alias']})?$/", $this->path());
                }

                $info = pathinfo($this->name());
                $pattern = preg_quote($info['dirname'], '/');

                return preg_match("/^{$pattern}\/[\d-]+\/{$info['basename']}$/", $this->path());
            }

            return false;
        }

        return parent::available();
    }

    public function getColumns() {
        if ($this->columns === null) {
            $this->columns = [];

            $names = $this->export ? $this->exportColumns() : $this->columns();

            foreach ($this->table()->getColumns($names) as $name => $column) {
                if ($column->invisible() && !$column->editable() && !$column->isWrapper()) {
                    continue;
                }

                if ($column->isCounter() && !$column->relation()['self-referencing']) {
                    if (!$this->permitted("{$this->node()}/{$column->alias()}")) {
                        continue;
                    }
                }

                if ($column->listStyle() === 'hidden') {
                    continue;
                }

                if ($column->tab()) {
                    if (!$names || !in_array($name, $names)) {
                        continue;
                    }
                }

                $this->columns[$name] = $column;
            }
        }

        return $this->columns;
    }

    public function remix($styles) {
        return $styles;
    }

    protected function getParentRelation() {
        return $this->table()->getParentRelation();
    }

    protected function init() {
        $table = $this->table();

        foreach ($table->getRelations() as $alias => $relation) {
            if ($relation['type'] === 'composition' && !$relation['junction']) {
                $name = "{$alias}_count";

                if (!isset($table->{$name})) {
                    $table->add($name, "{$alias}.count");
                }
            }
        }
    }

    protected function wrap() {
        $form = parent::wrap();

        if (@$form['export']) {
            $this->export = true;
        }

        $columns = $this->getColumns();
        $search = @$form['q'];

        if ($search) {
            $search = json_decode(base64_urldecode($search), true);
        } else {
            $search = $this->defaultSearch();
        }

        if ($search) {
            $columns = $this->filters() ? $this->table()->getColumns($this->filters()) : $columns;
            $conditions = [];

            foreach ($columns as $name => $column) {
                foreach ([$name, "-{$name}"] as $token) {
                    $value = @$search[$token];

                    if (is_array($value)) {
                        $values = [];

                        foreach ($value as $item) {
                            $item = urldecode($item);

                            if ($column->searchStyle() === 'like' || $column->validate($item) === true) {
                                $values[] = $item;
                            }
                        }

                        if ($values) {
                            $conditions[$token] = $values;
                        }
                    } else if ($value !== null) {
                        $value = urldecode($value);

                        if ($column->searchStyle() === 'like' || $column->validate($value) === true) {
                            $conditions[$token] = $value;
                        }
                    }
                }
            }

            if ($conditions) {
                $criteria = Criteria::createAnd();

                foreach ($columns as $name => $column) {
                    $from = @$conditions[$name];
                    $to = @$conditions["-{$name}"];

                    if ($from === null && $to === null) {
                        continue;
                    }

                    switch ($column->searchStyle()) {
                    case 'like':
                        if (is_array($from)) {
                            $like = Criteria::createOr();

                            foreach ($from as $tag) {
                                $like->add($column->like("%{$tag}%", true));
                            }

                            $criteria->add($like);
                        } else {
                            $criteria->add($column->like("%{$from}%", true));
                        }
                        break;
                    case 'between':
                        if ($from !== $to && !$column->association() && !$column->options()) {
                            if ($from === null) {
                                $criteria->add($column->lessThanOrEqual($to));
                            } else if ($to === null) {
                                $criteria->add($column->greaterThanOrEqual($from));
                            } else {
                                $criteria->add($column->between($from, $to));
                            }
                            break;
                        }
                    default:
                        if ($column->isJunction()) {
                            $criteria->add($column->overlap($from));
                        } else {
                            $criteria->add(is_array($from) ? $column->in($from) : $column->equal($from));
                        }
                    }

                    $column->inSearch(true);
                }

                $this->conditions($conditions);
                $this->criteria($criteria);
            }
        }

        $relation = $this->getParentRelation();

        if ($relation) {
            $args = $this->args();
            $form[$relation['column']->name()] = $args ? $args[0] : null;
        }

        return $form;
    }

    protected function process($form) {
        $criteria = $this->criteria();
        $sublist = @$form['sublist'];

        if ($this->export || $sublist) {
            $page = 1;
            $size = 0;
        } else {
            $page = intval(@$form['p']);
            $size = intval(@$form['s']);

            if ($page <= 0) {
                $page = $this->defaultPage();
            }

            if ($size <= 0) {
                $setting = $this->loadSetting();
                $size = @$setting['pageSize'] ?: $this->defaultPageSize();
            }
        }

        $orders = preg_split('/[, ]/', @$form['o'], 0, PREG_SPLIT_NO_EMPTY);

        if ($this->passive() && !$criteria && !$this->export) {
            $count = 0;
            $data = null;
        } else {
            $form[] = $criteria;
            $form[] = $this->groupFilter(@$form['g']);

            if ($this->export) {
                $args = @$form['args'];

                if ($args && is_array($args)) {
                    $form[] = $this->table()->id->in($args);
                }
            }

            $model = $this->table()->model();
            $count = $model->count($form);

            if ($count <= ($page - 1) * $size) {
                $page = $size ? intval(ceil($count / $size)) : 0;
            }

            $data = $count ? $model->query($form, $orders ?: $this->defaultRanking(), $size, $page) : [];
        }

        return $this->subprocess($form, [
            'success' => true,
            'view' => $sublist ? $this->sublistView() : ($this->export ? $this->exportView() : null),
            'count' => $count,
            'data' => $data,
            'page' => $page ?: 1,
            'size' => $size,
            'orders' => $orders,
        ]);
    }

    private function groupFilter($group) {
        if (!$group) {
            return null;
        }

        $criteria = Criteria::createAnd();
        $table = $this->table();
        $enable = $table->enableTime();
        $disable = $table->disableTime();
        $now = date(cfg('system.timestamp'));

        switch ($group) {
        case 1:
            if ($enable) {
                $criteria->add($table->{$enable}->notNull()->lessThanOrEqual($now));
            }
            if ($disable) {
                $criteria->add($table->{$disable}->isNull()->or()->greaterThan($now));
            }
            break;
        case 2:
            if ($disable) {
                $criteria->add($table->{$disable}->notNull()->lessThanOrEqual($now));
            }
            if ($enable) {
                $criteria = Criteria::createOr($table->{$enable}->isNull()->or()->greaterThan($now), $criteria);
            }
            break;
        case 3:
            if ($enable) {
                $criteria->add($table->{$enable}->notNull()->greaterThan($now));
            }
            break;
        case 4:
            if ($disable) {
                if ($enable) {
                    $criteria->add($table->{$enable}->notNull()->lessThanOrEqual($now));
                }
                $criteria->add($table->{$disable}->notNull()->greaterThan($now));
            }
            break;
        }

        return $criteria;
    }

}
