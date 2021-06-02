<?php //>

namespace matrix\db\criterion;

class IsNull extends AbstractCriterion {

    protected function build($dialect, $expression) {
        return "{$expression} IS NULL";
    }

}
