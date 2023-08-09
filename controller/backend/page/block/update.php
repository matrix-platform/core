<?php //>

return new class('Block') extends matrix\web\backend\UpdateController {

    use matrix\web\backend\block\Save;

    protected function wrap() {
        $data = $this->table()->model()->get($this->args()[0]);

        $this->module(load_cfg("module/{$data['module']}"));

        return $this->wrapModule(parent::wrap());
    }

};
