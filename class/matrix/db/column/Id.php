<?php //>

namespace matrix\db\column;

class Id extends Integer {

    public function __construct($values) {
        parent::__construct($values + [
            'invisible' => true,
            'readonly' => true,
            'required' => true,
            'sequence' => 'base_id',
        ]);

        $this->table()->id($this->name());
    }

    public function generate($value) {
        return $value ?? db()->next($this->sequence());
    }

}
