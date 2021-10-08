<?php //>

namespace matrix\db\column;

class Id {

    use type\Integer;

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
