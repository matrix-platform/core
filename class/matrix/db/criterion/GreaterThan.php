<?php //>

namespace matrix\db\criterion;

class GreaterThan extends AbstractCriterion {

    protected function build($dialect, $expression) {
        return "{$expression} > ?";
    }

}
