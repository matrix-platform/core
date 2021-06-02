<?php //>

namespace matrix\db;

use matrix\db\criterion\Helper;
use matrix\utility\ValueObject;

abstract class Column extends ValueObject {

    use Helper;

    protected static $defaults = [
        'mapping' => 'name',
    ];

    public function associate($alias, $foreign, $parent = false, $target = 'id') {
        $this->table()->register([
            'type' => 'association',
            'column' => $this,
            'alias' => $alias,
            'foreign' => $foreign,
            'target' => $target,
            'parent' => $parent,
        ]);

        if ($parent) {
            $this->invisible(true)->readonly(true);
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

}
