<?php //>

namespace matrix\web\backend\bundle;

use matrix\web\backend\Controller;

class GetBundle extends Controller {

    public function __construct() {
        $this->values = ['view' => 'backend/bundle/form.php'];
    }

    public function available() {
        if ($this->method() === 'POST') {
            $pattern = preg_quote($this->name(), '/');

            return preg_match("/^{$pattern}[\w]+\/[\w-]+$/", $this->path());
        }

        return false;
    }

    public function menuNode() {
        return preg_replace('/^\/backend\/(.+\/)[\w-]+$/', '$1', $this->path());
    }

    protected function process($form) {
        list($folder, $file) = $this->args();

        $node = rtrim($this->node(), '/');
        $allow = USER_ID === 1 ? null : preg_split('/\|/', cfg("backend.{$node}"));

        if ($allow === null || in_array($file, $allow)) {
            list($category) = preg_split('/\//', $node, 0, PREG_SPLIT_NO_EMPTY);

            $prefix = "{$category}/{$folder}.{$file}";
            $category = $category === 'i18n' ? ($category . '/' . LANGUAGE) : $category;
            $folder = $folder === 'base' ? $category : "{$category}/{$folder}";
            $default = union_resource("{$folder}/{$file}.php");
        }

        if (empty($default)) {
            return ['error' => 'error.data-not-found'];
        }

        return [
            'success' => true,
            'data' => array_replace_recursive($default, load_data("{$folder}/{$file}")),
            'default' => $default,
            'prefix' => $prefix,
        ];
    }

}
