<?php //>

$errors = [];

foreach ($result as $error) {
    $type = @$error['type'];

    $errors[] = [
        'name' => $error['name'],
        'message' => $error['message'] ?? i18n("validation.{$type}", $type),
    ];
}

$controller->response()->json(['type' => 'validation', 'errors' => $errors]);
