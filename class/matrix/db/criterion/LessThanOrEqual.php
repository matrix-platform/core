<?php //>

namespace matrix\db\criterion;

class LessThanOrEqual extends AbstractCriterion {

    protected function build($dialect, $expression) {
        return "{$expression} <= ?";
    }

}
