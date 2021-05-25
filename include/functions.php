<?php //>

use matrix\view\Native;
use matrix\view\Twig;

function find_resource($path) {
    foreach (RESOURCE_FOLDERS as $folder) {
        $file = $folder . $path;

        if (file_exists($file)) {
            return $file;
        }
    }

    return false;
}

function isolate_require() {
    return require func_get_arg(0);
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
