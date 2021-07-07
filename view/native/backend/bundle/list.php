<?php //>

use matrix\utility\Fn;

$path = $controller->node();

//--

$result['breadcrumbs'] = Fn::breadcrumbs($controller->menus(), $path, []);

//--

$result['styles'] = [
    ['column' => ['unordered' => true], 'i18n' => 'backend.bundle-name', 'name' => 'name', 'readonly' => true, 'type' => 'text'],
    ['column' => ['unordered' => true], 'i18n' => 'backend.bundle-remark', 'name' => 'remark', 'readonly' => true, 'type' => 'text'],
];

//--

$actions = [];

if (!isset($actions['view']) && $controller->permitted("{$path}/")) {
    $actions['view'] = [
        'link' => !$controller->modalForm(),
        'path' => "{$path}/{{ id }}",
        'ranking' => 100,
    ];
}

$result['actions'] = $actions;
$result['switches'] = [];

//--

$result['path'] = $path;

//--

resolve(cfg('backend.list'))->render($controller, $form, $result);
