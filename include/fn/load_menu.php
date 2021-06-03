<?php //>

return function ($names) {
    $bundle = [];

    foreach ($names as $name) {
        $menus = load_resource("menu/{$name}.php");

        foreach ($menus as $path => $menu) {
            $menu['i18n'] = "menu/{$name}.{$path}";

            $bundle[$path] = $menu;
        }
    }

    return $bundle;
};
