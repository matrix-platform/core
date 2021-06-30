<?php //>

namespace matrix\web\backend\authority;

use matrix\utility\Fn;

trait Authorization {

    private $menus;
    private $permissions;

    public function menus() {
        if ($this->menus === null) {
            $this->menus = Fn::load_menu(explode('|', cfg('backend.menus')));
        }

        return $this->menus;
    }

    public function permitted($node) {
        $menus = $this->menus();
        $menu = @$menus[$node];

        if ($menu) {
            $path = @$menu['group'] ? $node : $menu['parent'];
            $permissions = $this->permissions();
            $tag = $menu['tag'];

            if (@$permissions[$path][$tag] || $tag === 'user' || USER_ID <= ($tag === 'system' ? 1 : 2)) {
                return $menu;
            }
        }

        return false;
    }

    private function permissions() {
        if ($this->permissions === null) {
            $user = $this->user();

            $u = load_data("permission/User/{$user['id']}");
            $g = load_data("permission/Group/{$user['group_id']}");

            $this->permissions = $u ? ($g ? array_replace_recursive($g, $u) : $u) : $g;
        }

        return $this->permissions;
    }

}
