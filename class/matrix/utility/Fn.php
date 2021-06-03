<?php //>

namespace matrix\utility;

class Fn {

    public static function __callStatic($name, $args) {
        static $functions = [];

        if (!key_exists($name, $functions)) {
            $functions[$name] = load_resource("include/fn/{$name}.php");
        }

        return call_user_func_array($functions[$name], $args);
    }

}
