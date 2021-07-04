<?php //>

namespace matrix\web\backend\bundle;

use matrix\web\backend\Controller;

class ListBundle extends Controller {

    public function __construct() {
        $this->values = ['view' => 'backend/bundle/list.php'];
    }

    public function available() {
        if ($this->method() === 'POST') {
            $pattern = preg_quote($this->name(), '/');

            return preg_match("/^{$pattern}\/([\w]+)$/", $this->path());
        }

        return false;
    }

    public function menuNode() {
        return preg_replace('/^\/backend\/(.+)$/', '$1', $this->path());
    }

    protected function process($form) {
        $node = $this->node();

        list($category, $folder) = preg_split('/\//', $node, 0, PREG_SPLIT_NO_EMPTY);

        $category = $category === 'i18n' ? ($category . '/' . LANGUAGE) : $category;
        $folder = $folder === 'base' ? $category : "{$category}/{$folder}";
        $files = [];

        foreach (RESOURCE_FOLDERS as $res) {
            $path = "{$res}{$folder}";

            if (is_dir($path)) {
                $files = array_merge($files, scandir($path));
            }
        }

        $files = array_unique($files);

        sort($files);

        $allow = USER_ID === 1 ? null : preg_split('/\|/', cfg("backend.{$node}"));
        $data = [];

        foreach ($files as $file) {
            $info = pathinfo($file);

            if (@$info['extension'] === 'php') {
                $name = $info['filename'];

                if ($allow === null || in_array($name, $allow)) {
                    $data[] = [
                        'id' => $name,
                        'name' => $name,
                        'remark' => i18n("{$node}.{$name}", ''),
                    ];
                }
            }
        }

        return ['success' => true, 'data' => $data];
    }

}
