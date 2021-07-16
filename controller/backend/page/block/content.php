<?php //>

return new class('Block') extends matrix\web\backend\GetController {

    use matrix\web\backend\block\Form;

    protected function init() {
        $this->columns('module', 'title', 'padding_y', 'color', 'bg_color', 'enable_time', 'disable_time', 'ranking');
    }

    protected function postprocess($form, $result) {
        $data = $result['data'];
        $prefix = "module/{$data['module']}";

        $this->prefix($prefix);
        $this->module(load_cfg($prefix));

        $result['data'] = array_merge($data, json_decode($data['extra'], true));

        return $result;
    }

};
