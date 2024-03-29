<?php //>

logging('error-response')->debug($controller->name(), $form);
logging('error-response')->debug($controller->name(), $result ?: []);

$error = $result['error'] ?? 'error.unknown';
$message = @$result['message'] ?: i18n($error, '');
$data = @$result['data'];

if ($data) {
    $message = render($message, $data);
}

if (PHP_SAPI === 'cli') {
    echo "{$message}\n";
} else {
    $controller->response()->json(['type' => 'error', 'error' => $error, 'message' => $message, 'extra' => @$result['extra']]);
}
