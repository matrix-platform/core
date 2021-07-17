<?php //>

$controller = $result['controller'];
$form = $result['form'];
$model = $controller->table()->model();
$node = $controller->menuNode();

//--

$result['type'] = 'ranking';

//--

if ($form) {
    $parents = $model->parents($form);
    $titles = array_filter(array_column($parents, '.title'), 'is_string');
} else {
    $titles = [];
}

$titles[] = i18n($controller->menus()[$node]['i18n']);

$result['titles'] = $titles;

//--

$data = [];

foreach ($result['data'] as $row) {
    $data[] = ['id' => $row['id'], 'title' => $model->toString($row)];
}

$result['data'] = $data;

//--

$buttons = ['cancel' => ['ranking' => 100]];

if ($controller->permitted("{$node}/update")) {
    $buttons['update'] = ['path' => 'deployment/update/' . $result['path'], 'ranking' => 200];
}

$result['buttons'] = $buttons;

//--

resolve(cfg('backend.deployment-modal'))->render($controller, $form, $result);
