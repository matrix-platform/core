<?php //>

return function ($menus) {
    $nodes = [];

    foreach ($menus as $path => &$node) {
        if (empty($node['ranking'])) {
            continue;
        }

        $parent = $node['parent'];

        if (key_exists($parent, $menus)) {
            $menus[$parent]['nodes'][$path] = &$node;
        } else {
            $nodes[$path] = &$node;
        }
    }

    return $nodes;
};
