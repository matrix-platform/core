<?php //>

namespace matrix\db\column;

class Ranking {

    use type\Integer;

    public function __construct($values) {
        $this->values = $values + [
            'formStyle' => 'integer',
            'searchStyle' => false,
            'sequence' => 'base_ranking',
            'tab' => 'other',
            'validation' => 'integer',
        ];

        $this->table()->ranking($this->name());
    }

    public function generate($value) {
        return $value ?: db()->next($this->sequence());
    }

    public function regenerate($value) {
        return $this->generate($value);
    }

}
