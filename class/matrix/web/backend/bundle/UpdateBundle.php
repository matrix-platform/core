<?php //>

namespace matrix\web\backend\bundle;

use matrix\web\Attachment;
use matrix\web\backend\Controller;
use matrix\web\backend\Wrapper;

class UpdateBundle extends Controller {

    use Wrapper;

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
        $data = union_resource("{$folder}/{$file}.php");
        $columns = [];

        foreach ($data as $name => $ignore) {
            $class = cfg("style/{$prefix}.{$name}");

            if ($class) {
                $columns[$name] = new $class([]);
            }
        }

        $this->columns($columns)->data($data)->folder($folder);
    }

    protected function wrap() {
        $form = parent::wrap();

        foreach ($this->columns() as $name => $column) {
            $form = $this->wrapInput($column, $form, $name);
        }

        return $form;
    }

    protected function validate($form) {
        $errors = [];

        foreach ($this->columns() as $name => $column) {
            $value = @$form[$name];

            if ($value === null) {
                if ($column->required()) {
                    $errors[] = ['name' => $name, 'type' => 'required'];
                }
            } else {
                $type = $column->validate($value);

                if ($type !== true) {
                    $errors[] = ['name' => $name, 'type' => $type];
                }
            }
        }

        return $errors;
    }

    protected function process($form) {
        $node = dirname($this->node());
        $allow = USER_ID === 1 ? null : preg_split('/\|/', cfg("backend.{$node}"));
        list(1 => $file) = $this->args();

        if ($allow === null || in_array($file, $allow)) {
            $data = $this->data();
        }

        if (empty($data)) {
            return ['error' => 'error.data-not-found'];
        }

        $diff = [];

        foreach ($data as $name => $value) {
            $new = @$form[$name];

            if ($new === null) {
                continue;
            }

            if ($new instanceof Attachment) {
                $diff[$name] = $new->save();
            } else if ($new !== $value) {
                $diff[$name] = $new;
            }
        }

        $file = create_folder(get_data_file($this->folder(), false)) . '/' . $file;

        if (file_exists($file)) {
            $info = pathinfo($file);

            rename($file, "{$info['dirname']}/.{$info['basename']}-" . microtime(true) . '-' . USER_ID);
        }

        file_put_contents($file, $diff ? json_encode($diff, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : '{}');

        return ['success' => true];
    }

}
