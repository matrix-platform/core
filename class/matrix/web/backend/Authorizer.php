<?php //>

namespace matrix\web\backend;

use matrix\utility\Fn;

trait Authorizer {

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

            if (@$permissions[$path][$menu['tag']] || USER_ID === 1) {
                return $menu;
            }
        }

        return false;
    }

    private function permissions() {
        if ($this->permissions === null) {
            $user = $this->user();

            $u = Fn::load_permission('user', $user['id']);
            $g = Fn::load_permission('group', $user['group_id']);

            $this->permissions = $u ? ($g ? array_replace_recursive($g, $u) : $u) : $g;
        }

        return $this->permissions;
    }

}
