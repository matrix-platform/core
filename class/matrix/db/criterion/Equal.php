<?php //>

namespace matrix\db\criterion;

class Equal extends AbstractCriterion {

    protected function build($dialect, $expression) {
        return "{$expression} = ?";
    }

}
