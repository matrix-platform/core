<?php //>

$data = $result['data'];
$file = $result['file'];
$path = APP_HOME . 'files/' . $file;

if ($data['path'] === $file) {
    $size = $data['size'];
    $type = $data['mime_type'];
} else {
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $size = filesize($path);
    $type = finfo_file($finfo, $path);
    finfo_close($finfo);
}

header_remove();

header("Content-Length: {$size}");
header("Content-Type: {$type}");

readfile($path);
