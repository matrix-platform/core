<?php //>

return new class('Block') extends matrix\web\backend\UpdateController {

    use matrix\web\backend\block\Save;

    protected function wrap() {
        $form = parent::wrap();

        $this->module(load_cfg("module/{$form['module']}"));

        return $this->wrapModule($form);
    }

    protected function subprocess($form, $result) {
        if ($form['module'] !== $result['data']['module']) {
            return ['error' => 'error.update-failed'];
        }

        return $result;
    }

};
