<?php //>

namespace matrix\db\criterion;

class NotILike extends AbstractCriterion {

    protected function build($dialect, $expression) {
        return "LOWER({$expression}) NOT LIKE LOWER(?)";
    }

}
