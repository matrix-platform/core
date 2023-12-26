<?php //>

$param = @$form['sublist'] ? '?sublist=1' : '';
$delegate = $result['controller'];
$form = $result['form'];
$model = $delegate->table()->model();
$node = $delegate->menuNode();

//--

$result['type'] = 'ranking';

//--

if ($form) {
    $parents = $model->parents($form);
    $titles = array_filter(array_column($parents, '.title'), 'is_string');
} else {
    $titles = [];
}

$titles[] = i18n($delegate->menus()[$node]['i18n']);

$result['titles'] = $titles;

//--

if (!$result['image']) {
    $data = [];

    foreach ($result['data'] as $row) {
        $data[] = ['id' => $row['id'], 'title' => $model->toString($row)];
    }

    $result['data'] = $data;
}

//--

$buttons = ['cancel' => ['ranking' => 100]];

if ($delegate->permitted("{$node}/update")) {
    $buttons['update'] = ['path' => "deployment/update/{$result['path']}{$param}", 'ranking' => 200];
}

$result['buttons'] = $buttons;

//--

lookup(cfg('backend.deployment-modal'))->render($controller, $form, $result);
