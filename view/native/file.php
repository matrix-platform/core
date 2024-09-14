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

if (preg_match("/^bytes=([\d]+)-([\d]*)$/", @$_SERVER['HTTP_RANGE'], $matches)) {
    $begin = intval($matches[1]);
    $end = $matches[2] ? intval($matches[2]) : ($begin + (1024 * 1024) - 1);

    if ($end >= $size) {
        $end = $size - 1;
    }

    $length = $end - $begin + 1;

    $handle = fopen($path, "rb");

    if ($begin) {
        fseek($handle, $begin);
    }

    echo fread($handle, $length);

    fclose($handle);

    $controller->response()->status(206)->headers([
        'Content-Range' => "bytes {$begin}-{$end}/{$size}",
        'Content-Length' => $length,
    ]);
} else {
    $controller->response()->file($path)->headers([
        'Accept-Ranges' => 'bytes',
        'Content-Disposition' => 'inline;filename="' . addslashes($data['name']) . '"',
        'Content-Length' => $size,
        'Content-Type' => $type,
    ]);
}
