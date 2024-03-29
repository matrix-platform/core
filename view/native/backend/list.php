<?php //>

use matrix\db\column\Ranking;
use matrix\utility\Func;

$node = $controller->node();
$path = preg_replace('/^\/backend\/(.+)$/', '$1', $controller->path());
$table = $controller->table();

//--

$list = $table->model()->parents($form);
$titles = array_filter(array_column($list, '.title'), 'is_string');

$result['subtitle'] = array_pop($titles);

//--

$result['breadcrumbs'] = Func::breadcrumbs($controller->menus(), $node, $list);

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

if ($table->exportable() && !isset($controls['export'])) {
    $controls['export'] = [
        'least' => 0,
        'parameters' => ['export' => 1] + array_intersect_key($form, array_flip(['g', 'o', 'q'])),
        'path' => $path,
        'ranking' => 300,
    ];
}

if (!isset($controls['deploy'])) {
    $ranking = ltrim($table->ranking(), '-');

    if ($ranking && $table->{$ranking} instanceof Ranking) {
        $controls['deploy'] = [
            'least' => 0,
            'path' => 'deployment/' . base64_urlencode($path),
            'ranking' => 400,
        ];
    }
}

$result['controls'] = array_filter($controls);

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

foreach ($table->getColumns($controller->filters() ?: []) as $name => $column) {
    $search = $column->searchStyle();

    if ($search === false) {
        continue;
    }

    $options = is_array($column->options());

    $filters[] = [
        'column' => $column,
        'name' => $name,
        'search' => $options ? null : $search,
        'type' => $options ? 'select' : ($search === 'like' ? 'text' : $column->formStyle()),
    ];
}

$result['filters'] = $filters;

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
        'name' => $name,
        'readonly' => !$column->editable(),
        'type' => $type,
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

        $options = is_array($column->options());

        $filters[] = [
            'column' => $column,
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

if ($table->cloneable() && !isset($actions['clone']) && $controller->permitted("{$node}/new")) {
    $actions['clone'] = [
        'link' => !$controller->modalForm(),
        'path' => "{$path}/new/{{ id }}",
        'ranking' => 200,
    ];
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

lookup($view)->render($controller, $form, $result);
