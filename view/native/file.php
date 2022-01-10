<?php //>

$data = $result['data'];
$file = $result['file'];

$folder = $data['privilege'] ? (APP_HOME . 'files/') : FILES_HOME;
$path = "{$folder}{$file}";

if ($data['path'] === $file) {
    $size = $data['size'];
    $type = $data['mime_type'];
} else {
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $size = filesize($path);
    $type = finfo_file($finfo, $path);
    finfo_close($finfo);
}

$controller->response()->file($path)->headers([
    'Content-Length' => $size,
    'Content-Type' => $type,
]);
