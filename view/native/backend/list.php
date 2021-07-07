<?php //>

use matrix\utility\Fn;

$node = $controller->node();
$path = preg_replace('/^\/backend\/(.+)$/', '$1', $controller->path());
$table = $controller->table();

//--

$list = $table->model()->parents($form);
$titles = array_filter(array_column($list, '.title'), 'is_string');

$result['subtitle'] = array_pop($titles);

//--

$result['breadcrumbs'] = Fn::breadcrumbs($controller->menus(), $node, $list);

//--

$controls = $controller->controls() ?: [];

if (!isset($controls['new']) && $controller->permitted("{$node}/new")) {
    $controls['new'] = [
        'link' => !$controller->modalForm(),
        'path' => "{$path}/new",
        'ranking' => 100,
    ];
}

if (!isset($controls['delete']) && $controller->permitted("{$node}/delete")) {
    $controls['delete'] = [
        'least' => 1,
        'path' => "{$node}/delete",
        'ranking' => 200,
    ];
}

if (!isset($controls['export']) && $controller->permitted("{$node}/export")) {
    $controls['export'] = [
        'least' => 0,
        'parameters' => array_intersect_key($form, array_flip(['g', 'o', 'q'])),
        'path' => "{$path}/export",
        'ranking' => 300,
    ];
}

$result['controls'] = $controls;

//--

$enable = $table->enableTime();
$disable = $table->disableTime();

if ($enable) {
    $result['groups'] = $disable ? [0, 1, 2, 3, 4] : [0, 1, 2, 3];
} else {
    $result['groups'] = $disable ? [0, 1, 2, 4] : [];
}

//--

$filters = [];

foreach ($controller->filters() ?: [] as $name => $column) {
    $search = $column->searchStyle();

    if ($search === false) {
        continue;
    }

    $options = $column->options();

    $filters[] = [
        'column' => $column,
        'i18n' => "table/{$table->name()}.{$name}",
        'name' => $name,
        'search' => $options ? null : $search,
        'type' => $options ? 'select' : ($search === 'like' ? 'text' : $column->formStyle()),
    ];
}

$result['filters'] = $filters;

//--

$styles = [];

foreach ($controller->getColumns() as $name => $column) {
    $options = $column->options();

    $styles[] = [
        'column' => $column,
        'i18n' => "table/{$table->name()}.{$name}",
        'name' => $name,
        'readonly' => !$column->editable(),
        'type' => $options ? 'select' : $column->listStyle(),
    ];
}

$result['styles'] = $controller->remix($styles);

//--

if (!$filters) {
    $selected = false;

    foreach ($result['styles'] as $style) {
        $column = @$style['column'];
        $search = $column ? $column->searchStyle() : false;

        if ($search === false) {
            continue;
        }

        if ($selected === false && $column->inSearch()) {
            $selected = count($filters);
        }

        $options = $column->options();

        $filters[] = [
            'column' => $column,
            'i18n' => $style['i18n'],
            'name' => $style['name'],
            'search' => $options ? null : $search,
            'type' => $options ? 'select' : ($search === 'like' ? 'text' : $column->formStyle()),
        ];
    }

    if ($filters) {
        $filters[$selected ?: 0]['selected'] = true;

        $result['simple_filters'] = $filters;
    }
}

//--

$orders = [];

foreach ($result['orders'] as $index => $name) {
    if ($name[0] === '-') {
        $orders[substr($name, 1)] = -1 - $index;
    } else {
        $orders[$name] = $index + 1;
    }
}

$result['orders'] = $orders;

//--

$actions = $controller->actions() ?: [];
$switches = $controller->switches() ?: [];

if ($controller->permitted("{$node}/")) {
    if (!isset($actions['view'])) {
        $actions['view'] = [
            'link' => !$controller->modalForm(),
            'path' => "{$node}/{{ id }}",
            'ranking' => 100,
        ];
    }

    if ($enable || $disable) {
        $now = date(cfg('system.timestamp'));

        foreach ($result['data'] as &$data) {
            $data['.visible'] = true;

            if ($enable) {
                if (!$data[$enable] || strcmp($data[$enable], $now) > 0) {
                    $data['.visible'] = false;
                }
            }

            if ($disable) {
                if ($data[$disable] && strcmp($data[$disable], $now) < 0) {
                    $data['.visible'] = false;
                }
            }
        }

        if (!isset($switches['visible'])) {
            $switches['visible'] = ['ranking' => 100];
        }
    }
}

$result['actions'] = $actions;
$result['switches'] = $switches;

//--

$result['buttons'] = $controller->buttons() ?: [];

//--

$result['path'] = $path;
$result['parameters'] = array_intersect_key($form, array_flip(['g', 'o', 'p', 'q', 's']));

if ($result['parameters']) {
    $result['backward'] = ['r' => base64_urlencode(http_build_query($result['parameters']))];
}

//--

$view = $controller->customView() ?: cfg('backend.list');

resolve($view)->render($controller, $form, $result);
