<?php //>

use matrix\utility\Fn;

$node = $controller->menu()['parent'];
$table = $controller->table();

//--

$list = $table->model()->parents($result['data']);
$list[] = $result['data'];
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

    $type = $column->formStyle();

    if ($type === 'hidden') {
        continue;
    }

    if ($column->options() && $type !== 'radio' && $type !== 'select') {
        $type = $column->association() ? 'select' : 'radio';
    }

    $styles[] = [
        'column' => $column,
        'disabled' => $column->readonly() || $column->disabled(),
        'i18n' => "table/{$table->name()}.{$name}",
        'name' => $name,
        'required' => $controller->isRequired($column),
        'type' => $type,
    ];
}

$result['styles'] = $controller->remix($styles, $list);

if ($table->versionable()) {
    $result['styles'][] = ['name' => '__version__', 'type' => 'hidden'];
}

//--

$buttons = $controller->buttons() ?: [];

$buttons[] = [
    'ranking' => 100,
    'type' => 'cancel',
];

if ($controller->permitted("{$node}/update")) {
    $buttons[] = [
        'ranking' => 200,
        'type' => 'update',
    ];
}

$result['buttons'] = $buttons;

//--

$result['path'] = $node;

//--

$view = $controller->customView() ?: cfg(@$form['args'] === 'modal' ? 'backend.form-modal' : 'backend.form');

resolve($view)->render($controller, $form, $result);