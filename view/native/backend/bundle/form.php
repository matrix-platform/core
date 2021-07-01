<?php //>

use matrix\db\column\Text;
use matrix\utility\Fn;

$path = $controller->menu()['parent'];
$prefix = $result['prefix'];

//--

$id = $controller->args()[1];

$result['subtitle'] = i18n($prefix, $id);

//--

$data = ['.title' => $result['subtitle']];

$result['breadcrumbs'] = Fn::breadcrumbs($controller->menus(), $controller->node(), [$data]);

//--

$styles = [];

foreach ($result['default'] as $name => $value) {
    $options = cfg("style/{$prefix}.{$name}.options");

    if ($options) {
        if ($options instanceof Closure) {
            $options = call_user_func($options);
            $type = 'select';
        } else {
            $options = load_options($options);
            $type = 'radio';
        }

        $column = ['options' => $options];
    } else {
        $class = cfg("style/{$prefix}.{$name}") ?: Text::class;
        $column = new $class([]);

        $type = $column->formStyle();
    }

    $styles[] = [
        'column' => $column,
        'i18n' => "{$prefix}.{$name}",
        'name' => $name,
        'placeholder' => $value,
        'type' => $type,
    ];
}

$result['styles'] = $styles;

//--

$buttons = [];

$buttons[] = [
    'ranking' => 100,
    'type' => 'cancel',
];

if ($controller->permitted("{$path}/update")) {
    $buttons[] = [
        'ranking' => 200,
        'type' => 'update',
    ];
}

$result['buttons'] = $buttons;

//--

$result['data']['id'] = $id;
$result['path'] = $path;

//--

resolve(cfg('backend.form'))->render($controller, $form, $result);
