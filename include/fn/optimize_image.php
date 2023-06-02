<?php //>

use Intervention\Image\ImageManagerStatic;

return function ($image, $width = 0, $height = 0) {
    $data = is_array($image) ? $image : load_file_data($image);

    if (!$data) {
        return $image;
    }

    if (strtok($data['mime_type'], '/') !== 'image') {
        return $data['path'];
    }

    $type = strtok('/');

    if (webp()) {
        $to = 'webp';
    } else {
        $to = in_array($type, ['gif', 'png']) ? $type : 'jpeg';
    }

    $path = $data['path'];

    if (!$width && !$height) {
        $max = cfg('system.max-thumb-size');

        if ($data['width'] >= $data['height']) {
            if ($data['width'] > $max) {
                $width = $max;
            }
        } else {
            if ($data['height'] > $max) {
                $height = $max;
            }
        }
    }

    if ($width > 0 || $height > 0) {
        $w = $data['width'];
        $h = $data['height'];
        $r = $w / $h;

        if ($width && $w > $width) {
            $w = $width;
            $h = intval($w / $r);
        }

        if ($height && $h > $height) {
            $h = $height;
            $w = intval($h * $r);
        }

        $file = "{$path}.{$w}.{$to}";
    } else {
        $w = 0;
        $file = "{$path}.{$to}";
    }

    $folder = $data['privilege'] ? PRIV_FILES_HOME : FILES_HOME;
    $optimize = "{$folder}{$file}";

    if (!file_exists($optimize)) {
        try {
            $img = ImageManagerStatic::make("{$folder}{$path}");

            if ($w) {
                $img->resize($w, null, function ($constraint) {
                    $constraint->aspectRatio();
                });
            }

            $img->save($optimize);
            $img->destroy();
        } catch (Exception $ignore) {
            return $path;
        }
    }

    return $file;
};
