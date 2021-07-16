<?php //>

return new class('Block') extends matrix\web\backend\BlankController {

    use matrix\web\backend\block\Form;

    public function available() {
        if ($this->method() === 'POST') {
            $info = pathinfo($this->name());
            $action = $info['basename'];

            $info = pathinfo($info['dirname']);
            $pattern = preg_quote($info['dirname'], '/');

            return preg_match("/^{$pattern}\/[\d]+\/{$info['basename']}\/{$action}\/[\w-]+$/", $this->path());
        }

        return false;
    }

    protected function init() {
        $table = $this->table();

        $table->module->disabled(true);

        $this->buttons(['insert' => ['path' => 'page/{{ page_id }}/block/insert', 'ranking' => 200]]);

        $this->columns('module', 'title', 'padding_y', 'color', 'bg_color', 'enable_time', 'disable_time', 'ranking');
    }

    protected function preprocess($form) {
        $args = $this->args();

        $form['module'] = count($args) > 1 ? $args[1] : null;

        $prefix = "module/{$form['module']}";
        $module = load_cfg($prefix);

        foreach ($module['fields'] as $name => $field) {
            $form[$name] = $field->default();
        }

        $this->prefix($prefix);
        $this->module($module);

        return $form;
    }

};
