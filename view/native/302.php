<?php //>

if (defined('AJAX')) {
    resolve('raw.php')->render($controller, $form, [
        'type' => 'location',
        'path' => $result['path'],
    ]);
} else {
    header("Location: {$result['path']}");
}
