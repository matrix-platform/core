<?php //>

if (defined('AJAX')) {
    $controller->response()->json(['type' => 'location', 'path' => $result['path']]);
} else {
    $controller->response()->redirect($result['path']);
}
