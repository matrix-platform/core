<?php //>

use matrix\web\backend\UpdateController;

return new class() extends matrix\web\UserController {

    protected function process($form) {
        $path = preg_replace('/^visible:(.*)\/(\d+)$/', '/backend/$1/update/$2', @$form['id']);
        $controller = routing($path, 'POST');

        if ($controller instanceof UpdateController) {
            if ($controller->permitted($controller->menuNode())) {
                $data = [];
                $visible = filter_var(@$form['value'], FILTER_VALIDATE_BOOLEAN);

                $table = $controller->table();
                $enable = $table->enableTime();
                $disable = $table->disableTime();

                if ($enable) {
                    $data[$enable] = $visible ? date(cfg('system.timestamp')) : null;
                }

                if ($disable) {
                    $data[$disable] = $visible ? null : date(cfg('system.timestamp'));
                }

                if ($data) {
                    $data['id'] = $controller->args()[0];

                    $data = $table->model()->update($data);

                    if ($data === null) {
                        return ['error' => 'error.data-not-found'];
                    }

                    if ($data === false) {
                        return ['error' => 'error.update-failed'];
                    }

                    return ['success' => true];
                }
            }

            return ['view' => '403.php'];
        } else {
            return ['view' => '404.php'];
        }
    }

};
