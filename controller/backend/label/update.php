<?php //>

return new class() extends matrix\web\UserController {

    protected function init() {
        $this->view(cfg('backend.label-updated'));
    }

    protected function process($form) {
        list($name, $key) = preg_split('/\./', $form['name'], 2);

        $path = 'i18n/' . constant('LANGUAGE') . '/' . $name;
        $bundle = union_resource("{$path}.php");
        $file = get_data_file($path, false);

        if (!is_writable(create_folder(dirname($file)))) {
            return ['error' => 'error.update-failed'];
        }

        if (file_exists($file)) {
            if (!is_file($file) || !is_readable($file)) {
                return ['error' => 'error.update-failed'];
            }

            $data = json_decode(file_get_contents($file), true);

            unlink($file);
        } else {
            $data = [];
        }

        if ($form['content'] === null || $form['content'] === @$bundle[$key]) {
            unset($data[$key]);
        } else {
            $data[$key] = $form['content'];
        }

        if ($data) {
            $data = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

            file_put_contents($file, $data);
        } else {
            $data = '{}';
        }

        file_put_contents($file . '.' . db()->next('base_manipulation') . '.' . USER_ID, $data);

        return ['success' => true];
    }

};
