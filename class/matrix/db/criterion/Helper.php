<?php //>

namespace matrix\db\criterion;

trait Helper {

    public function between($from, $to) {
        return new Between($this, [$from, $to]);
    }

    public function equal($value) {
        return new Equal($this, [$value]);
    }

    public function greaterThan($value) {
        return new GreaterThan($this, [$value]);
    }

    public function greaterThanOrEqual($value) {
        return new GreaterThanOrEqual($this, [$value]);
    }

    public function in(...$values) {
        if (count($values) === 1 && is_array($values[0])) {
            $values = $values[0];
        }

        return new In($this, $values);
    }

    public function isNull() {
        return new IsNull($this, []);
    }

    public function lessThan($value) {
        return new LessThan($this, [$value]);
    }

    public function lessThanOrEqual($value) {
        return new LessThanOrEqual($this, [$value]);
    }

    public function like($value, $insensitive = false) {
        if ($insensitive) {
            return new ILike($this, [$value]);
        } else {
            return new Like($this, [$value]);
        }
    }

    public function notBetween($from, $to) {
        return new NotBetween($this, [$from, $to]);
    }

    public function notEqual($value) {
        return new NotEqual($this, [$value]);
    }

    public function notIn(...$values) {
        if (count($values) === 1 && is_array($values[0])) {
            $values = $values[0];
        }

        return new NotIn($this, $values);
    }

    public function notLike($value, $insensitive = false) {
        if ($insensitive) {
            return new NotILike($this, [$value]);
        } else {
            return new NotLike($this, [$value]);
        }
    }

    public function notNull() {
        return new NotNull($this, []);
    }

}
