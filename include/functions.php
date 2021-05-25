<?php //>

use matrix\view\Native;
use matrix\view\Twig;

function cfg($token) {
    list($name, $key) = preg_split('/\./', $token, 2);

    $bundle = load_cfg($name);

    return @$bundle[$key];
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

function isolate_require() {
    return require func_get_arg(0);
}

function load_bundle($name) {
    static $bundles = [];

    if (!key_exists($name, $bundles)) {
        $bundle = union_resource("{$name}.php");

        if ($bundle) {
            $file = get_data_file($name);

            if ($file !== false) {
                $data = json_decode(file_get_contents($file), true);
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

function resolve($view) {
    switch (pathinfo($view, PATHINFO_EXTENSION)) {
    case 'twig':
        return new Twig($view);
    }

    return new Native($view);
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
