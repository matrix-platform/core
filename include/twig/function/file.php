<?php //>

return new Twig\TwigFunction('file', function ($path) {
    $file = get_data_file("files/{$path}", false);

    if (is_file($file)) {
        return json_decode(file_get_contents($file), true);
    }

    $data = model('File')->find(['path' => $path]);

    if ($data) {
        create_folder(dirname($file));
        file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }

    return $data;
});
