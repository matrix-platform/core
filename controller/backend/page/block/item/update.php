<?php //>

return new class('BlockItem') extends matrix\web\backend\UpdateController {

    use matrix\web\backend\block\Save;

    protected function wrap() {
        $data = $this->table()->model()->get($this->args()[0]);

        $block = model('Block')->get($data['block_id']);
        $module = load_cfg("module/{$block['module']}");

        $this->module(load_cfg("sub-module/{$module['sub-module']}"));

        return $this->wrapModule(parent::wrap());
    }

};
