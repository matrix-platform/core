<?php //>

namespace matrix\db\criterion;

class LessThan extends AbstractCriterion {

    protected function build($dialect, $expression) {
        return "{$expression} < ?";
    }

}
