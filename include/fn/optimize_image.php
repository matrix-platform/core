<?php //>

return function ($image, $width = 0, $height = 0) {
    $data = is_array($image) ? $image : load_file_data($image);

    if (!$data || strtok($data['mime_type'], '/') !== 'image') {
        return $image;
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

    $folder = $data['privilege'] ? (APP_HOME . 'files/') : FILES_HOME;

    if (!file_exists($folder . $file)) {
        $loader = "imagecreatefrom{$type}";
        $source = "{$folder}{$path}";

        if (function_exists($loader)) {
            $img = @call_user_func($loader, $source);
        } else {
            $img = @imagecreatefromstring(file_get_contents($source));
        }

        if ($img) {
            if ($w) {
                $img = imagescale($img, $w);
            }

            if ($type === 'png' && $to === 'webp') {
                imagepalettetotruecolor($img);
            }

            $exif = @exif_read_data($source);

            switch (@$exif['Orientation']) {
            case 3:
                $angle = 180;
                break;
            case 6:
                $angle = 270;
                break;
            case 8:
                $angle = 90;
                break;
            default:
                $angle = 0;
            }

            if ($angle) {
                $img = imagerotate($img, $angle, 0);
            }

            call_user_func("image{$to}", $img, $folder . $file);

            imagedestroy($img);
        } else {
            $file = $path;
        }
    }

    return $file;
};
