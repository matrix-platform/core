<?php //>

return new class('BlockItem') extends matrix\web\backend\ListController {

    public function remix($styles) {
        $prefix = $this->prefix();
        $bundle = load_i18n($prefix) ?: [];
        $table = $this->table();
        $fields = [];

        foreach ($this->module()['fields'] as $name => $field) {
            if (isset($table->{$name})) {
                $type = $field->listStyle();

                if ($type === 'hidden') {
                    continue;
                }

                $field->i18n(key_exists($name, $bundle) ? "{$prefix}.{$name}" : "label.{$name}");
                $field->multilingual($table->{$name}->multilingual());

                $fields[] = [
                    'column' => $field,
                    'name' => $name,
                    'readonly' => true,
                    'type' => $field->options() ? 'select' : $type,
                ];
            }
        }

        array_splice($styles, 0, 0, $fields);

        return $styles;
    }

    protected function init() {
        $this->columns([]);
    }

    protected function postprocess($form, $result) {
        $block = model('Block')->get($form['block_id']);
        $module = load_cfg("module/{$block['module']}");
        $prefix = "sub-module/{$module['sub-module']}";

        $this->prefix($prefix);
        $this->module(load_cfg($prefix));

        return $result;
    }

};
