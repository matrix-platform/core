<?php //>

namespace matrix\db\column;

class Ranking extends Integer {

    public function __construct($values) {
        parent::__construct($values + ['sequence' => 'base_ranking']);

        $this->table()->ranking($this->name());
    }

    public function generate($value) {
        return $value ?? db()->next($this->sequence());
    }

    public function regenerate($value) {
        return $this->generate($value);
    }

}
