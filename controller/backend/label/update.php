<?php //>

return new class() extends matrix\web\backend\Controller {

    protected function init() {
        $this->view(cfg('backend.label-updated'));
    }

    protected function process($form) {
        list($name, $key) = preg_split('/\./', $form['name'], 2);

        $path = 'i18n/' . LANGUAGE . '/' . $name;
        $bundle = union_resource("{$path}.php");
        $file = get_data_file($path, false);

        if (!is_writable(create_folder(dirname($file)))) {
            return ['error' => 'error.update-failed'];
        }

        if (file_exists($file)) {
            $data = json_decode(file_get_contents($file), true);
            $info = pathinfo($file);

            rename($file, "{$info['dirname']}/.{$info['basename']}-" . microtime(true) . '-' . USER_ID);
        } else {
            $data = [];
        }

        if (@$form['content'] === null || $form['content'] === @$bundle[$key]) {
            unset($data[$key]);
        } else {
            $data[$key] = $form['content'];
        }

        file_put_contents($file, $data ? json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : '{}');

        return ['success' => true];
    }

};
