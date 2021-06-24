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
        ]);

        if ($parent) {
            $this->invisible(true)->readonly(true);
        } else {
            $this->options(function ($column) {
                static $options;

                if ($options === null) {
                    $relation = $column->table()->getRelation($column->association());

                    $model = $relation['foreign']->model();
                    $name = $relation['target']->name();
                    $options = [];

                    foreach ($model->query($relation['filter']) as $item) {
                        $options[$item[$name]] = $model->toString($item);
                    }
                }

                return $options;
            });
        }

        return $this->association($alias);
    }

    public function composite($alias, $foreign, $target = null) {
        $this->table()->register([
            'type' => 'composition',
            'column' => $this,
            'alias' => $alias,
            'foreign' => $foreign,
            'target' => $target,
        ]);

        return $this;
    }

    abstract public function convert($value);

    public function expression($dialect, $language = null, $prefix = null) {
        $mapping = $this->mapping();

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
