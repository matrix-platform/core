<?php //>

use matrix\db\Connection;
use matrix\view\Native;
use matrix\view\Twig;
use Monolog\Handler\FirePHPHandler;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;
use Twig\Environment;
use Twig\Loader\ArrayLoader;

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
        $name = constant("{$prefix}_NAME");
        $user = constant("{$prefix}_USER");
        $password = constant("{$prefix}_PASSWORD");

        if ($name && $user) {
            $instances[$prefix] = new Connection($name, $user, $password);
        } else {
            $instances[$prefix] = null;
        }
    }

    return $instances[$prefix];
}

function decrypt_data($text, $key = null, $iv = null) {
    $text = base64_urldecode($text);

    if ($key === null) {
        $key = cfg('system.default-key');
    }

    if ($iv === null) {
        $iv = cfg('system.default-iv');
    }

    return openssl_decrypt(substr($text, 0, -16), 'AES-256-GCM', $key, OPENSSL_RAW_DATA, $iv ?: $key, substr($text, -16));
}

function encrypt_data($payload, $key = null, $iv = null, $safe = false) {
    if ($key === null) {
        $key = cfg('system.default-key');
    }

    if ($iv === null) {
        $iv = cfg('system.default-iv');
    }

    $data = openssl_encrypt($payload, 'AES-256-GCM', $key, OPENSSL_RAW_DATA, $iv ?: $key, $tag);

    return $safe ? base64_urlencode($data . $tag) : base64_encode($data . $tag);
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

function get_image_url($image, $width = 0, $height = 0) {
    if ($image) {
        return get_url(APP_PATH . 'images/' . $width . '/' . $height . '/' . base64_urlencode($image));
    }

    return null;
}

function get_url($path) {
    if (defined('BASE_URL')) {
        return BASE_URL . $path;
    } else {
        $protocol = defined('HTTPS') ? 'https' : 'http';

        return "{$protocol}://{$_SERVER['HTTP_HOST']}{$path}";
    }
}

function i18n($token, $default = null) {
    list($name, $key) = preg_split('/\./', $token, 2);

    $bundle = load_i18n($name);

    return $bundle[$key] ?? $default ?? "{{$token}}";
}

function isolate_require() {
    return require func_get_arg(0);
}

function load_bundle($name, $key = null, $value = null) {
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

    if ($key !== null) {
        if ($value === null) {
            unset($bundles[$name][$key]);
        } else {
            $bundles[$name][$key] = $value;
        }
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

function load_file_data($path) {
    $file = get_data_file("files/{$path}", false);

    if (is_file($file)) {
        return json_decode(file_get_contents($file), true);
    }

    if (is_string($path)) {
        $data = model('File')->find(['path' => $path]);
    } else {
        $data = model('File')->get($path);
    }

    if ($data) {
        create_folder(dirname($file));
        file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }

    return $data;
}

function load_fn($name, $wrap = false) {
    $fn = load_resource("include/fn/{$name}.php");

    if ($wrap) {
        return function () use ($fn) {
            return $fn;
        };
    }

    return $fn;
}

function load_i18n($name, $language = LANGUAGE) {
    return load_bundle("i18n/{$language}/{$name}");
}

function load_options($name) {
    $options = [];

    foreach (load_i18n("options/{$name}") as $value => $title) {
        if (!is_null($title)) {
            $options[$value] = ['title' => $title];
        }
    }

    return $options;
}

function load_resource($path, $resolve = true, $cache = true) {
    static $resources = [];

    if ($resolve) {
        $file = find_resource($path);
    } else {
        $file = is_file($path) ? $path : false;
    }

    if ($file === false) {
        return null;
    }

    if ($cache) {
        if (!key_exists($file, $resources)) {
            $resources[$file] = isolate_require($file);
        }

        return $resources[$file];
    }

    return isolate_require($file);
}

function logging($name = 'message') {
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

function lookup($view) {
    switch (pathinfo($view, PATHINFO_EXTENSION)) {
    case 'twig':
        return new Twig($view);
    }

    return new Native($view);
}

function model($name) {
    return table($name)->model();
}

function now() {
    static $now;

    if ($now === null) {
        $now = date(cfg('system.timestamp'));
    }

    return $now;
}

function render($template, $data) {
    $twig = new Environment(new ArrayLoader(['template' => $template]));

    return $twig->render('template', $data);
}

function routing($path, $method) {
    $args = [];
    $current = '';
    $tokens = preg_split('/\//', $path, 0, PREG_SPLIT_NO_EMPTY);

    $candidates = [['/', 'index', $tokens]];

    while ($tokens) {
        $found = false;
        $token = array_shift($tokens);
        $name = "{$current}/{$token}";

        if (find_resource("controller{$name}.php")) {
            $found = true;
            $candidates[] = [$name, '', array_merge($args, $tokens)];
        }

        if ($tokens && find_resource("controller{$name}/")) {
            $found = true;

            if (find_resource("controller{$name}/content.php")) {
                $candidates[] = ["{$name}/", 'content', array_merge($args, $tokens)];
            }
        }

        if ($found) {
            $current = $name;
        } else {
            $args[] = $token;
        }
    }

    while ($candidates) {
        list($name, $file, $args) = array_pop($candidates);

        $controller = load_resource("controller{$name}{$file}.php");

        if (is_object($controller)) {
            $controller->args($args)->method($method)->name($name)->path($path);

            if ($controller->available()) {
                return $controller->verify() ? $controller : null;
            }
        }
    }

    return null;
}

function set_cfg($token, $value = null) {
    list($name, $key) = preg_split('/\./', $token, 2);

    load_bundle("cfg/{$name}", $key, $value);
}

function set_i18n($token, $value = null, $language = LANGUAGE) {
    list($name, $key) = preg_split('/\./', $token, 2);

    load_bundle("i18n/{$language}/{$name}", $key, $value);
}

function table($name, $cache = true) {
    static $cloneables, $exportables;

    if ($cloneables === null) {
        $cloneables = preg_split('/\|/', cfg('backend.cloneable-table'), 0, PREG_SPLIT_NO_EMPTY);
    }

    if ($exportables === null) {
        $exportables = preg_split('/\|/', cfg('backend.exportable-table'), 0, PREG_SPLIT_NO_EMPTY);
    }

    $table = load_resource("table/{$name}.php", true, $cache);

    if ($table) {
        if (in_array($name, $cloneables)) {
            $table->cloneable(true);
        }

        if (in_array($name, $exportables)) {
            $table->exportable(true);
        }

        return $table->name($name);
    }

    throw new Exception("Table `{$name}` not found.");
}

function today() {
    static $today;

    if ($today === null) {
        $today = date(cfg('system.date'));
    }

    return $today;
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

function validate($value, $options) {
    if (is_array($options)) {
        $validation = @$options['validation'];
    } else {
        $validation = $options;
        $options = [];
    }

    if ($validation instanceof Closure) {
        return call_user_func($validation, $value, $options);
    }

    foreach (preg_split('/\|/', $validation, 0, PREG_SPLIT_NO_EMPTY) as $type) {
        $validator = load_resource("include/validator/{$type}.php");

        if (call_user_func($validator, $value, $options) === false) {
            return $type;
        }
    }

    return true;
}

function webp() {
    static $supported;

    if ($supported === null) {
        if (strpos(@$_SERVER['HTTP_ACCEPT'], 'image/webp') === false && strpos(@$_SERVER['HTTP_USER_AGENT'], ' Chrome/') === false) {
            $supported = false;
        } else {
            $supported = true;
        }
    }

    return $supported;
}
