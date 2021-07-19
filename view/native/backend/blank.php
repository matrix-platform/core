<?php //>

use matrix\utility\Fn;

$node = $controller->menu()['parent'];
$path = preg_replace('/^\/backend\/(.+)\/[\w]+$/', '$1', $controller->path());
$table = $controller->table();

//--

$list = $table->model()->parents($form);
$list[] = $form;
$titles = array_filter(array_column($list, '.title'), 'is_string');

$result['subtitle'] = array_pop($titles);

//--

$result['breadcrumbs'] = Fn::breadcrumbs($controller->menus(), $controller->node(), $list);

//--

$styles = [];

foreach ($table->getColumns($controller->columns()) as $name => $column) {
    if ($column->invisible()) {
        continue;
    }

    $type = $column->blankStyle() ?: $column->formStyle();

    if ($type === 'hidden') {
        continue;
    }

    if ($column->options()) {
        if (!isset($form[$name])) {
            $form[$name] = $column->default();
        }

        if ($column->multiple()) {
            $type = $column->sortable() ? 'sortable-options' : 'select';
        } else if ($type !== 'radio' && $type !== 'select') {
            $type = $column->association() ? 'select' : 'radio';
        }
    }

    $styles[] = [
        'column' => $column,
        'disabled' => $column->disabled(),
        'i18n' => "table/{$table->name()}.{$name}",
        'name' => $name,
        'required' => $controller->isRequired($column),
        'type' => $type,
    ];
}

$result['styles'] = $controller->remix($styles);

//--

$buttons = $controller->buttons() ?: [];

if (!isset($buttons['cancel'])) {
    $buttons['cancel'] = ['ranking' => 100];
}

if (!isset($buttons['insert']) && $controller->permitted("{$node}/insert")) {
    $buttons['insert'] = ['path' => "{$path}/insert", 'ranking' => 200];
}

$result['buttons'] = $buttons;

//--

$result['data'] = $form;

//--

$view = $controller->customView() ?: cfg(@$form['args'] === 'modal' ? 'backend.form-modal' : 'backend.form');

resolve($view)->render($controller, $form, $result);
