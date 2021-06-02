<?php //>

namespace matrix\db\criterion;

class NotNull extends AbstractCriterion {

    protected function build($dialect, $expression) {
        return "{$expression} IS NOT NULL";
    }

}
