<?php //>

namespace matrix\db\criterion;

class NotLike extends AbstractCriterion {

    protected function build($dialect, $expression) {
        return "{$expression} NOT LIKE ?";
    }

}
