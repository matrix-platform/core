<?php //>

namespace matrix\db\criterion;

class Like extends AbstractCriterion {

    protected function build($dialect, $expression) {
        return "{$expression} LIKE ?";
    }

}
