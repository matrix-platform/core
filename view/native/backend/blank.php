<?php //>

use matrix\utility\Func;

$node = $controller->menu()['parent'];
$path = preg_replace('/^\/backend\/(.+?)\/[\w]+(\/[\d]+)?$/', '$1', $controller->path());
$suffix = preg_replace('/^.*?(\/[\d]+)?$/', '$1', $controller->path());
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

    $type = $column->blankStyle() ?: $column->formStyle();

    if ($type === 'hidden') {
        continue;
    }

    if (is_array($column->options())) {
        if (!isset($form[$name])) {
            $form[$name] = $column->default();
        }

        if ($column->multiple()) {
            $type = $column->sortable() ? 'sortable-options' : 'checkbox-group';
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
$param = @$form['sublist'] ? '?sublist=1' : '';

if (!isset($buttons['cancel'])) {
    $buttons['cancel'] = ['ranking' => 100];
}

if (!isset($buttons['insert']) && $controller->permitted("{$node}/insert")) {
    $buttons['insert'] = ['path' => "{$path}/insert{$suffix}{$param}", 'ranking' => 200];
}

$result['buttons'] = $buttons;

//--

$view = $controller->customView() ?: cfg(@$form['args'] === 'modal' ? 'backend.form-modal' : 'backend.form');

lookup($view)->render($controller, $form, $result);
