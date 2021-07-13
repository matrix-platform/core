<?php //>

$error = $result['error'] ?? 'error.unknown';
$message = $result['message'] ?? i18n($error, '');

if (PHP_SAPI === 'cli') {
    echo "{$message}\n";
} else {
    resolve('raw.php')->render($controller, $form, ['type' => 'error', 'error' => $error, 'message' => $message]);
}
