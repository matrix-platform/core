<?php //>

return new class('BlockItem') extends matrix\web\backend\BlankController {

    use matrix\web\backend\block\Form;

    protected function init() {
        $this->columns($this->table()->getColumns([
            'enable_time',
            'disable_time',
            'ranking',
        ]));
    }

    protected function preprocess($form) {
        $block = model('Block')->get($form['block_id']);
        $module = load_cfg("module/{$block['module']}");
        $prefix = "sub-module/{$module['sub-module']}";
        $module = load_cfg($prefix);

        foreach ($module['fields'] as $name => $field) {
            $form[$name] = $field->default();
        }

        $this->prefix($prefix);
        $this->module($module);

        return $form;
    }

};
