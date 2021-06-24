<?php //>

use matrix\utility\Fn;

$node = $controller->menu()['parent'];
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

foreach ($controller->columns() ?: $table->getColumns() as $name => $column) {
    if ($column->invisible()) {
        continue;
    }

    $type = $column->blankStyle() ?: $column->formStyle();

    if ($type === 'hidden') {
        continue;
    }

    if ($column->options()) {
        $form[$name] = $column->default();

        if ($type !== 'radio' && $type !== 'select') {
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

$result['styles'] = $controller->remix($styles, $list);

//--

$buttons = $controller->buttons() ?: [];

$buttons[] = [
    'ranking' => 100,
    'type' => 'cancel',
];

if ($controller->permitted("{$node}/insert")) {
    $buttons[] = [
        'ranking' => 200,
        'type' => 'insert',
    ];
}

$result['buttons'] = $buttons;

//--

$result['data'] = $form;
$result['path'] = preg_replace('/^\/backend\/(.+)\/[\w]+$/', '$1', $controller->path());

//--

$view = $controller->customView() ?: cfg(@$form['args'] === 'modal' ? 'backend.form-modal' : 'backend.form');

resolve($view)->render($controller, $form, $result);
