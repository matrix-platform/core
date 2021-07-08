<?php //>

return new class('BlockItem') extends matrix\web\backend\InsertController {

    use matrix\web\backend\block\Save;

    protected function wrap() {
        $form = parent::wrap();

        $block = model('Block')->get($form['block_id']);
        $module = load_cfg("module/{$block['module']}");

        $this->module(load_cfg("sub-module/{$module['sub-module']}"));

        return $this->wrapModule($form);
    }

};
