<?php //>

namespace matrix\web\backend\bundle;

use matrix\web\Attachment;
use matrix\web\backend\Wrapper;

trait Form {

    use Wrapper;

    protected function wrap() {
        $form = parent::wrap();

        foreach ($this->inputs() as $name => $input) {
            $form = $this->wrapInput($input, $form, $name);
        }

        return $form;
    }

    protected function validate($form) {
        $errors = [];

        foreach ($this->inputs() as $name => $input) {
            $value = @$form[$name];

            if ($value === null) {
                if ($input->required()) {
                    $errors[] = ['name' => $name, 'type' => 'required'];
                }
            } else {
                $type = $input->validate($value);

                if ($type !== true) {
                    $errors[] = ['name' => $name, 'type' => $type];
                }
            }
        }

        return $errors;
    }

    protected function init() {
        $data = union_resource("{$this->folder()}/{$this->file()}.php");
        $inputs = [];
        $prefix = $this->prefix();

        foreach ($data as $name => $ignore) {
            $class = cfg("style/{$prefix}.{$name}");

            if ($class) {
                $inputs[$name] = new $class([]);
            }
        }

        $this->data($data)->inputs($inputs);
    }

    private function save($form, $file, $data) {
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
