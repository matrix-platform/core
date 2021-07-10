<?php //>

namespace matrix\web\backend\bundle;

use matrix\web\backend\Controller;

class UpdateBundle extends Controller {

    use Form {
        init as initForm;
    }

    public function __construct() {
        $this->values = ['view' => 'backend/save-success.php'];
    }

    public function available() {
        if ($this->method() === 'POST') {
            $info = pathinfo($this->name());
            $pattern = preg_quote($info['dirname'], '/');

            return preg_match("/^{$pattern}\/[\w]+\/{$info['basename']}\/[\w-]+$/", $this->path());
        }

        return false;
    }

    public function menuNode() {
        return preg_replace('/^\/backend\/(.+)\/[\w-]+$/', '$1', $this->path());
    }

    protected function init() {
        list($category) = preg_split('/\//', $this->node(), 0, PREG_SPLIT_NO_EMPTY);
        list($folder, $file) = $this->args();

        $prefix = "{$category}/{$folder}.{$file}";
        $category = $category === 'i18n' ? ($category . '/' . LANGUAGE) : $category;
        $folder = $folder === 'base' ? $category : "{$category}/{$folder}";

        $this->folder($folder)->file($file)->prefix($prefix);

        $this::initForm();
    }

    protected function process($form) {
        $node = dirname($this->node());
        $allow = USER_ID === 1 ? null : preg_split('/\|/', cfg("backend.{$node}"));
        $file = $this->file();

        if ($allow === null || in_array($file, $allow)) {
            $data = $this->data();
        }

        if (empty($data)) {
            return ['error' => 'error.data-not-found'];
        }

        return $this->save($form, $file, $data);
    }

}
