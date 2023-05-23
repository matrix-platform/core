<?php //>

use matrix\db\column\Ranking;

$node = $controller->node();
$path = preg_replace('/^\/backend\/(.+)$/', '$1', $controller->path());
$table = $controller->table();

//--

$controls = $controller->controls() ?: [];

if (!isset($controls['new']) && $controller->permitted("{$node}/new")) {
    $controls['new'] = [
        'link' => false,
        'parameters' => ['sublist' => 1],
        'path' => "{$path}/new",
        'ranking' => 100,
    ];
}

if (!isset($controls['delete']) && $controller->permitted("{$node}/delete")) {
    $controls['delete'] = [
        'least' => 1,
        'parameters' => ['sublist' => 1],
        'path' => "{$node}/delete",
        'ranking' => 200,
    ];
}

if ($table->exportable() && !isset($controls['export'])) {
    $controls['export'] = [
        'least' => 0,
        'parameters' => ['export' => 1],
        'path' => $path,
        'ranking' => 300,
    ];
}

if (!isset($controls['deploy'])) {
    $ranking = ltrim($table->ranking(), '-');

    if ($ranking && $table->{$ranking} instanceof Ranking) {
        $controls['deploy'] = [
            'least' => 0,
            'parameters' => ['sublist' => 1],
            'path' => 'deployment/' . base64_urlencode($path),
            'ranking' => 400,
        ];
    }
}

$result['controls'] = $controls;

//--

$enable = $table->enableTime();
$disable = $table->disableTime();

//--

$styles = [];

foreach ($controller->getColumns() as $name => $column) {
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
        'i18n' => $column->i18n(),
        'name' => $name,
        'readonly' => !$column->editable(),
        'type' => $type,
    ];
}

$result['styles'] = $controller->remix($styles);

//--

$actions = $controller->actions() ?: [];
$switches = $controller->switches() ?: [];

if ($controller->permitted("{$node}/")) {
    if (!isset($actions['view'])) {
        $actions['view'] = [
            'link' => false,
            'path' => "{$node}/{{ id }}?sublist=1",
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

if ($table->cloneable() && !isset($actions['clone']) && $controller->permitted("{$node}/new")) {
    $actions['clone'] = [
        'link' => false,
        'path' => "{$path}/new/{{ id }}?sublist=1",
        'ranking' => 200,
    ];
}

$result['actions'] = $actions;
$result['switches'] = $switches;

//--

$result['path'] = $path;
$result['backward'] = [];

//--

$view = cfg('backend.sublist');

lookup($view)->render($controller, $form, $result);
