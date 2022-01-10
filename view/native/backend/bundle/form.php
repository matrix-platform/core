<?php //>

use matrix\db\column\Text;
use matrix\utility\Func;

$file = @$result['file'];
$path = $result['path'];
$prefix = $result['prefix'];

//--

$data = key_exists('subtitle', $result) ? ['.title' => $result['subtitle']] : null;

$result['breadcrumbs'] = Func::breadcrumbs($controller->menus(), $controller->node(), [$data]);

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

if ($file && !isset($buttons['cancel'])) {
    $buttons['cancel'] = ['ranking' => 100];
}

if (!isset($buttons['update']) && $controller->permitted("{$path}/update")) {
    $buttons['update'] = ['path' => "{$path}/update" . ($file ? "/{$file}" : ''), 'ranking' => 200];
}

$result['buttons'] = $buttons;

//--

$view = cfg(@$form['args'] === 'modal' ? 'backend.form-modal' : 'backend.form');

lookup($view)->render($controller, $form, $result);
