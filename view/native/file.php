<?php //>

$data = $result['data'];
$file = $result['file'];

$folder = $data['privilege'] ? (APP_HOME . 'files/') : FILES_HOME;
$path = "{$folder}{$file}";

if ($data['path'] === $file) {
    $size = $data['size'];
    $type = $data['mime_type'];
} else {
    $size = filesize($path);
    $type = mime_content_type($path);
}

$controller->response()->file($path)->headers([
    'Content-Length' => $size,
    'Content-Type' => $type,
]);
