<?php //>

use matrix\utility\Func;

$node = $controller->menu()['parent'];
$table = $controller->table();

//--

$list = $table->model()->parents($result['data']);
$list[] = $result['data'];
$titles = array_filter(array_column($list, '.title'), 'is_string');

$result['subtitle'] = array_pop($titles);

//--

$result['breadcrumbs'] = Func::breadcrumbs($controller->menus(), $controller->node(), $list);

//--

$styles = [];

foreach ($table->getColumns($controller->columns()) as $name => $column) {
    if ($column->invisible()) {
        continue;
    }

    $type = $column->formStyle();

    if ($type === 'hidden') {
        continue;
    }

    if (is_array($column->options())) {
        if ($column->multiple()) {
            $type = $column->sortable() ? 'sortable-options' : 'select';
        } else if ($type !== 'radio' && $type !== 'select') {
            $type = $column->association() ? 'select' : 'radio';
        }
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

$result['styles'] = $controller->remix($styles);

if ($table->versionable()) {
    $result['styles'][] = ['name' => '__version__', 'type' => 'hidden'];
}

//--

$buttons = $controller->buttons() ?: [];

if (!isset($buttons['cancel'])) {
    $buttons['cancel'] = ['ranking' => 100];
}

if (!isset($buttons['update']) && $controller->permitted("{$node}/update")) {
    $buttons['update'] = ['path' => "{$node}/update/{{ id }}", 'ranking' => 200];
}

$result['buttons'] = $buttons;

//--

$view = $controller->customView() ?: cfg(@$form['args'] === 'modal' ? 'backend.form-modal' : 'backend.form');

resolve($view)->render($controller, $form, $result);
