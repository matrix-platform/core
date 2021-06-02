<?php //>

namespace matrix\db\criterion;

class NotEqual extends AbstractCriterion {

    protected function build($dialect, $expression) {
        return "{$expression} <> ?";
    }

}
