<?php //>

use matrix\db\Connection;
use matrix\view\Native;
use matrix\view\Twig;
use Monolog\Handler\FirePHPHandler;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

function base64_urldecode($data) {
    return base64_decode(str_replace(['-', '_'], ['+', '/'], $data));
}

function base64_urlencode($data) {
    return str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($data));
}

function cfg($token) {
    list($name, $key) = preg_split('/\./', $token, 2);

    $bundle = load_cfg($name);

    return @$bundle[$key];
}

function create_folder($path) {
    if (!is_dir($path)) {
        create_folder(dirname($path));

        $origin = umask(0);

        mkdir($path, 0777);
        umask($origin);
    }

    return $path;
}

function db($prefix = 'DB') {
    static $instances = [];

    if (!key_exists($prefix, $instances)) {
        $name = @constant("{$prefix}_NAME");
        $user = @constant("{$prefix}_USER");
        $password = @constant("{$prefix}_PASSWORD");

        if ($name && $user) {
            $instances[$prefix] = new Connection($name, $user, $password);
        } else {
            $instances[$prefix] = null;
        }
    }

    return $instances[$prefix];
}

function find_resource($path) {
    foreach (RESOURCE_FOLDERS as $folder) {
        $file = $folder . $path;

        if (file_exists($file)) {
            return $file;
        }
    }

    return false;
}

function get_data_file($path, $verify = true) {
    $file = APP_DATA . $path;

    if (!$verify || is_file($file)) {
        return $file;
    }

    return false;
}

function i18n($token, $default = null) {
    list($name, $key) = preg_split('/\./', $token, 2);

    $bundle = load_i18n($name);

    return $bundle[$key] ?? $default ?? "{{$token}}";
}

function isolate_require() {
    return require func_get_arg(0);
}

function load_bundle($name) {
    static $bundles = [];

    if (!key_exists($name, $bundles)) {
        $bundle = union_resource("{$name}.php");

        if ($bundle) {
            $data = load_data($name);

            if ($data) {
                $bundle = array_replace_recursive($bundle, $data);
            }
        }

        $bundles[$name] = $bundle;
    }

    return $bundles[$name];
}

function load_cfg($name) {
    return load_bundle("cfg/{$name}");
}

function load_data($name) {
    $file = get_data_file($name);

    return $file === false ? [] : json_decode(file_get_contents($file), true);
}

function load_i18n($name, $language = LANGUAGE) {
    return load_bundle("i18n/{$language}/{$name}");
}

function load_resource($path, $resolve = true) {
    static $resources = [];

    if ($resolve) {
        $file = find_resource($path);
    } else {
        $file = is_file($path) ? $path : false;
    }

    if ($file === false) {
        return null;
    }

    if (!key_exists($file, $resources)) {
        $resources[$file] = isolate_require($file);
    }

    return $resources[$file];
}

function logger($name = 'message') {
    static $loggers = [];

    if (!key_exists($name, $loggers)) {
        $file = (PHP_SAPI === 'cli') ? "cli-{$name}" : $name;

        $handlers = [new RotatingFileHandler(create_folder(APP_LOG) . $file)];

        if (cfg('system.debug')) {
            $handlers[] = new FirePHPHandler();
        }

        $loggers[$name] = new Logger($name, $handlers);
    }

    return $loggers[$name];
}

function model($name) {
    return table($name)->model();
}

function resolve($view) {
    switch (pathinfo($view, PATHINFO_EXTENSION)) {
    case 'twig':
        return new Twig($view);
    }

    return new Native($view);
}

function table($name) {
    $table = load_resource("table/{$name}.php");

    if ($table) {
        return $table->name($name);
    }

    throw new Exception("Table `{$name}` not found.");
}

function union_resource($path) {
    $bundle = null;

    foreach (RESOURCE_FOLDERS as $folder) {
        $data = load_resource($folder . $path, false);

        if (is_array($data)) {
            $bundle = $bundle ? array_replace_recursive($data, $bundle) : $data;
        }
    }

    return $bundle;
}
