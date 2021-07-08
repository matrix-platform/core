<?php //>

return new class('Block') extends matrix\web\backend\InsertController {

    use matrix\web\backend\block\Save;

    protected function wrap() {
        $form = parent::wrap();

        $this->module(load_cfg("module/{$form['module']}"));

        return $this->wrapModule($form);
    }

};
