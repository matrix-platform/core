<?php //>

$data = $result['data'];
$file = $result['file'];

$folder = $data['privilege'] ? PRIV_FILES_HOME : FILES_HOME;
$path = "{$folder}{$file}";

if ($data['path'] === $file) {
    $size = $data['size'];
    $type = $data['mime_type'];
} else {
    $size = filesize($path);
    $type = mime_content_type($path);
}

$controller->response()->file($path)->headers([
    'Content-Disposition' => 'inline;filename="' . addslashes($data['name']) . '"',
    'Content-Length' => $size,
    'Content-Type' => $type,
]);
