<?php //>

$error = $result['error'] ?? 'error.unknown';

resolve('raw.php')->render($controller, $form, [
    'type' => 'error',
    'error' => $error,
    'message' => $result['message'] ?? i18n($error, ''),
]);
