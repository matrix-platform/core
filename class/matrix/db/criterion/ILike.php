<?php //>

namespace matrix\db\criterion;

class ILike extends AbstractCriterion {

    protected function build($dialect, $expression) {
        return "LOWER({$expression}) LIKE LOWER(?)";
    }

}
