<?php //>

$table = $controller->table();

//--

$styles = [];

foreach ($controller->getColumns() as $name => $column) {
    if ($column->invisible()) {
        continue;
    }

    $type = $column->listStyle();

    if (is_array($column->options())) {
        if ($column->multiple()) {
            $type = $column->sortable() ? 'sortable-options' : 'checkbox-group';
        } else if ($type !== 'radio' && $type !== 'select') {
            $type = $column->association() ? 'select' : 'radio';
        }
    }

    $styles[] = [
        'column' => $column,
        'i18n' => "table/{$table->name()}.{$name}",
        'name' => $name,
        'readonly' => !$column->editable(),
        'type' => $type,
    ];
}

$result['styles'] = $styles;

//--

$actions = [];

$actions['picker'] = [
    'picker' => true,
    'ranking' => 100,
];

$result['actions'] = $actions;

//--

lookup(cfg('backend.search-list'))->render($controller, $form, $result);