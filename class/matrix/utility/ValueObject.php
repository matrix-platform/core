<?php //>

namespace matrix\utility;

use Closure;

class ValueObject {

    protected $values;

    public function __construct($values = []) {
        $this->values = $values;
    }

    public function __call($name, $args) {
        if ($args) {
            $this->values[$name] = count($args) > 1 ? $args : $args[0];

            return $this;
        }

        if (key_exists($name, $this->values)) {
            $value = $this->values[$name];

            if ($value instanceof Closure) {
                return call_user_func($value, $this);
            } else {
                return $value;
            }
        }

        if (isset(static::$defaults) && key_exists($name, static::$defaults)) {
            return $this->{static::$defaults[$name]}();
        }

        return null;
    }

}
