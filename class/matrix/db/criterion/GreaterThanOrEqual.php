<?php //>

namespace matrix\db\criterion;

class GreaterThanOrEqual extends AbstractCriterion {

    protected function build($dialect, $expression) {
        return "{$expression} >= ?";
    }

}
