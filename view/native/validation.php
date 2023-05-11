<?php //>

logging('validation')->debug($controller->name(), $form);
logging('validation')->debug($controller->name(), $result);

$errors = [];

foreach ($result as $error) {
    $type = @$error['type'];

    $errors[] = [
        'name' => $error['name'],
        'message' => $error['message'] ?? i18n("validation.{$type}", $type),
        'type' => $type,
    ];
}

$controller->response()->json(['type' => 'validation', 'errors' => $errors]);
