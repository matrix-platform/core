<?php //>

namespace matrix\db;

use matrix\db\criterion\Helper;
use matrix\utility\ValueObject;

trait Column {

    use Helper, ValueObject;

    protected static $defaults = [
        'listStyle' => 'formStyle',
        'mapping' => 'name',
    ];

    public function associate($alias, $foreign, $parent = false, $filter = [], $target = 'id') {
        $this->table()->register([
            'type' => 'association',
            'column' => $this,
            'alias' => $alias,
            'foreign' => $foreign,
            'target' => $target,
            'parent' => $parent,
            'filter' => $filter,
            'names' => [$target],
        ]);

        if ($parent) {
            $this->invisible(true)->readonly(true);
        }

        $this->options(function ($column) {
            static $options;

            if ($options === null) {
                $relation = $column->table()->getRelation($column->association());
                $cascade = $relation['foreign']->getParentRelation();

                if ($cascade) {
                    $column->cascade($cascade);
                    $cascade_id = $cascade['column']->name();
                }

                $model = $relation['foreign']->model();
                $name = $relation['target']->name();
                $options = [];

                foreach ($model->query($relation['filter']) as $item) {
                    $option = ['title' => $model->toString($item)];

                    if ($cascade) {
                        $option['parent_id'] = $item[$cascade_id];
                    }

                    $options[$item[$name]] = $option;
                }
            }

            return $options;
        });

        return $this->association($alias);
    }

    public function composite($alias, $foreign, $junction = false, $target = null) {
        $this->table()->register([
            'type' => 'composition',
            'column' => $this,
            'alias' => $alias,
            'foreign' => $foreign,
            'target' => $target,
            'junction' => $junction,
        ]);

        return $this;
    }

    abstract public function convert($value);

    public function expression($dialect, $language = null, $prefix = null, $name = null, $select = false) {
        $mapping = $name ?: $this->mapping();

        if ($language !== null && $this->multilingual()) {
            $mapping = "{$mapping}__{$language}";
        }

        if ($prefix === null) {
            $prefix = $this->alias();
        }

        return "_{$prefix}.{$mapping}";
    }

    public function generate($value) {
        return $value;
    }

    public function regenerate($value) {
        return $value;
    }

    abstract public function type();

    public function validate($value) {
        return validate($value, $this->values);
    }

}
