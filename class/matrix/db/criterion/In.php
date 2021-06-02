<?php //>

namespace matrix\db\criterion;

class In extends AbstractCriterion {

    protected function build($dialect, $expression) {
        $count = count($this->values);

        switch ($count) {
        case 0:
            return '1 <> 1';

        case 1:
            return "{$expression} = ?";

        default:
            $values = implode(',', array_fill(0, $count, '?'));
            return "{$expression} IN ({$values})";
        }
    }

}
