<?php //>

namespace matrix\web\backend\block;

trait Form {

    public function remix($styles) {
        $prefix = $this->prefix();
        $bundle = load_i18n($prefix) ?: [];
        $table = $this->table();
        $fields = [];

        foreach ($this->module()['fields'] as $name => $field) {
            $type = $field->formStyle();

            if ($field->options()) {
                if ($field->multiple()) {
                    $type = $field->sortable() ? 'sortable-options' : 'checkbox-group';
                } else if ($type !== 'radio' && $type !== 'select') {
                    $type = 'radio';
                }
            }

            if (isset($table->{$name})) {
                $field->multilingual($table->{$name}->multilingual());
            }

            $fields[] = [
                'column' => $field,
                'i18n' => key_exists($name, $bundle) ? "{$prefix}.{$name}" : "label.{$name}",
                'name' => $name,
                'required' => $field->required(),
                'type' => $type,
            ];
        }

        array_splice($styles, -3, 0, $fields);

        $excludes = $this->module()['excludes'];

        if ($excludes) {
            $styles = array_filter($styles, function ($style) use ($excludes) {
                return !in_array($style['name'], $excludes);
            });
        }

        return $styles;
    }

}
