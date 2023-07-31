<?php //>

namespace matrix\web\backend\authority;

trait PermissionTree {

    public function remix($styles) {
        $menus = $this->menus();
        $nodes = [];

        foreach ($menus as $path => &$menu) {
            $parent = $menu['parent'];

            if (key_exists($parent, $menus)) {
                $tag = @$menu['tag'];

                if ($tag) {
                    $name = @$menu['group'] ? $path : $parent;

                    if ($tag !== 'system' && $tag !== 'user') {
                        $menus[$name]['tags'][$tag] = $tag;
                    }

                    if ($name === $parent) {
                        continue;
                    }
                }

                $menus[$parent]['nodes'][$path] = &$menu;
            } else if (@$menu['ranking']) {
                $nodes[$path] = &$menu;
            }
        }

        $styles[] = [
            'column' => ['i18n' => "table/{$this->table()->name()}.permissions", 'options' => $nodes],
            'name' => 'permissions',
            'type' => 'checkbox-tree',
        ];

        return $styles;
    }

}
