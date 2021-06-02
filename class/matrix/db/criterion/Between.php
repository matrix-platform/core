<?php //>

namespace matrix\db\criterion;

class Between extends AbstractCriterion {

    protected function build($dialect, $expression) {
        return "{$expression} BETWEEN ? AND ?";
    }

}
