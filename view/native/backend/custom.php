<?php //>

use matrix\utility\Func;

$data = @$result['data'] ?: $form;
$table = $controller->table();

$list = $table->model()->parents($data);
$list[] = $data;
$titles = array_filter(array_column($list, '.title'), 'is_string');

$result['subtitle'] = array_pop($titles);
$result['breadcrumbs'] = Func::breadcrumbs($controller->menus(), $controller->node(), $list);

lookup($controller->customView())->render($controller, $form, $result);
