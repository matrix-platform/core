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

if ($controller->permitted("{$path}/")) {
    $actions[] = [
        'ranking' => 100,
        'type' => 'view',
    ];
}

$result['actions'] = $actions;

//--

$result['path'] = $path;

//--

resolve(cfg('backend.list'))->render($controller, $form, $result);
