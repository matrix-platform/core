<?php //>

namespace matrix\db\criterion;

trait Helper {

    public function between($from, $to) {
        return new Monomer($this, 'between', [$from, $to]);
    }

    public function equal($value) {
        return new Monomer($this, 'equal', [$value]);
    }

    public function greaterThan($value) {
        return new Monomer($this, 'greaterThan', [$value]);
    }

    public function greaterThanOrEqual($value) {
        return new Monomer($this, 'greaterThanOrEqual', [$value]);
    }

    public function in(...$values) {
        if (count($values) === 1 && is_array($values[0])) {
            $values = $values[0];
        }

        return new Monomer($this, 'in', $values);
    }

    public function isNull() {
        return new Monomer($this, 'isNull', []);
    }

    public function lessThan($value) {
        return new Monomer($this, 'lessThan', [$value]);
    }

    public function lessThanOrEqual($value) {
        return new Monomer($this, 'lessThanOrEqual', [$value]);
    }

    public function like($value, $insensitive = false) {
        return new Monomer($this, $insensitive ? 'iLike' : 'like', [$value]);
    }

    public function notBetween($from, $to) {
        return new Monomer($this, 'notBetween', [$from, $to]);
    }

    public function notEqual($value) {
        return new Monomer($this, 'notEqual', [$value]);
    }

    public function notIn(...$values) {
        if (count($values) === 1 && is_array($values[0])) {
            $values = $values[0];
        }

        return new Monomer($this, 'notIn', $values);
    }

    public function notLike($value, $insensitive = false) {
        return new Monomer($this, $insensitive ? 'notILike' : 'notLike', [$value]);
    }

    public function notNull() {
        return new Monomer($this, 'notNull', []);
    }

}
