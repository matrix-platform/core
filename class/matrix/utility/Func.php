<?php //>

namespace matrix\utility;

class Func {

    public static function __callStatic($name, $args) {
        static $functions = [];

        if (!key_exists($name, $functions)) {
            $functions[$name] = load_fn($name);
        }

        return call_user_func_array($functions[$name], $args);
    }

}
