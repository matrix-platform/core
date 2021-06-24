<?php //>

return new class() extends matrix\web\UserController {

    protected function process($form) {
        $file = create_folder(APP_DATA . 'setting/') . USER_ID;
        $setting = file_exists($file) ? json_decode(file_get_contents($file), true) : [];
        $size = intval(@$form['s']);

        if (in_array($size, [20, 30, 50, 100])) {
            $setting['pageSize'] = $size;
        } else {
            unset($setting['pageSize']);
        }

        if ($setting) {
            file_put_contents($file, json_encode($setting, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        } else if (file_exists($file)) {
            unlink($file);
        }

        return ['success' => true, 'type' => 'refresh'];
    }

};
