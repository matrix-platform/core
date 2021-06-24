<?php //>

return function ($menus, $node, $list) {
    $breadcrumbs = [];
    $routable = true;

    while ($node) {
        $menu = $menus[$node];

        if ($routable) {
            $data = array_pop($list);

            if ($data) {
                $menu['subtitle'] = $data['.title'];

                if (key_exists('pattern', $menu)) {
                    $node = render($menu['pattern'], $data);
                }
            }

            $menu['path'] = $node;
            $routable = !@$menu['ranking'];
        }

        $breadcrumbs[] = $menu;
        $node = $menu['parent'];
    }

    return array_reverse($breadcrumbs);
};
