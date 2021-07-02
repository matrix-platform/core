<?php //>

return function ($menus, $node, $list) {
    $breadcrumbs = [];
    $routable = true;

    while ($node) {
        $menu = $menus[$node];

        if ($routable) {
            $data = array_pop($list);
            $path = $node;

            if ($data) {
                $menu['subtitle'] = @$data['.title'];

                if (key_exists('pattern', $menu)) {
                    $path = render($menu['pattern'], $data);
                }
            }

            $menu['path'] = $path;

            if (@$menu['ranking']) {
                if ($data) {
                    $breadcrumbs[] = $menu;
                    continue;
                }

                $routable = false;
            }
        }

        $breadcrumbs[] = $menu;
        $node = $menu['parent'];
    }

    return array_reverse($breadcrumbs);
};
