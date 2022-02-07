<?php //>

use matrix\utility\Func;

return new class() extends matrix\web\UserController {

    use matrix\web\backend\authority\Authorization;

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
        $nodes = Func::menu_tree($this->menus());

        return [
            'success' => true,
            'nodes' => $this->filter($nodes),
            'post_max_size' => $this->getBytes(ini_get('post_max_size')),
            'max_file_uploads' => $this->getBytes(ini_get('max_file_uploads')),
            'upload_max_filesize' => $this->getBytes(ini_get('upload_max_filesize')),
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
                if (!@$node['tag'] || !$this->permitted($path)) {
                    $node = null;
                }
            }
        }

        return array_filter($nodes);
    }

    private function getBytes($size) {
        $value = intval($size);

        switch (strtoupper(substr($size, -1))) {
        case 'G':
            return $value << 30;
        case 'K':
            return $value << 10;
        case 'M':
            return $value << 20;
        }

        return $value;
    }

};
