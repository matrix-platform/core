<?php //>

namespace matrix\db\criterion;

class NotBetween extends AbstractCriterion {

    protected function build($dialect, $expression) {
        return "{$expression} NOT BETWEEN ? AND ?";
    }

}
