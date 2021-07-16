<?php //>

return new class('BlockItem') extends matrix\web\backend\GetController {

    use matrix\web\backend\block\Form;

    protected function init() {
        $this->columns('enable_time', 'disable_time', 'ranking');
    }

    protected function postprocess($form, $result) {
        $data = $result['data'];
        $block = model('Block')->get($data['block_id']);
        $module = load_cfg("module/{$block['module']}");
        $prefix = "sub-module/{$module['sub-module']}";

        $this->prefix($prefix);
        $this->module(load_cfg($prefix));

        $result['data'] = array_merge($data, json_decode($data['extra'], true));

        return $result;
    }

};
