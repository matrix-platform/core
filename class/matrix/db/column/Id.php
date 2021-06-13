<?php //>

namespace matrix\db\column;

use matrix\db\Column;

class Id {

    use Column, type\Integer;

    public function __construct($values) {
        $this->values = $values + [
            'invisible' => true,
            'readonly' => true,
            'sequence' => 'base_id',
        ];

        $this->table()->id($this->name());
    }

    public function generate($value) {
        return $value ?: db()->next($this->sequence());
    }

}
