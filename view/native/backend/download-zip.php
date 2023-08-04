<?php //>

$name = tempnam(APP_DATA, '');

$zip = new ZipArchive();
$zip->open($name, ZIPARCHIVE::CREATE);

foreach ($result['files'] as $file) {
    $zip->addFile($file, basename($file));
}

$zip->close();

$result = [
    'type' => 'download',
    'filename' => "{$result['name']}.zip",
    'content' => base64_encode(file_get_contents($name)),
    'contentType' => 'application/zip',
];

unlink($name);

$controller->response()->json($result);
