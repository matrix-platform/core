<?php //>

use matrix\utility\Fn;

return new class() extends matrix\web\UserController {

    use matrix\web\backend\Authorizer;

    public function available() {
        if ($this->method() === 'GET') {
            $pattern = preg_quote($this->name(), '/');

            return preg_match("/^{$pattern}(\/.+)?$/", $this->path());
        }

        return false;
    }

    protected function init() {
        $this->view(cfg('backend.index'));
    }

    protected function process($form) {
        $nodes = Fn::menu_tree($this->menus());

        return [
            'success' => true,
            'nodes' => $this->filter($nodes),
        ];
    }

    private function filter($nodes) {
        foreach ($nodes as $path => &$node) {
            if (@$node['nodes']) {
                $node['nodes'] = $this->filter($node['nodes']);

                if (!$node['nodes']) {
                    $node = null;
                }
            } else {
                if (!$this->permitted($path)) {
                    $node = null;
                }
            }
        }

        return array_filter($nodes);
    }

};
