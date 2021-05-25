<?php //>

$errors = [];

foreach ($result as $error) {
    $type = @$error['type'];

    $errors[] = [
        'name' => $error['name'],
        'message' => $error['message'] ?? i18n("validation.{$type}", $type),
    ];
}

resolve('raw.php')->render($controller, $form, [
    'type' => 'validation',
    'errors' => $errors,
]);
