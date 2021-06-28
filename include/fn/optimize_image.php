<?php //>

return function ($image, $width = 0) {
    $folder = defined('FILES_HOME') ? FILES_HOME : (APP_HOME . 'www/files/');
    $file = $width ? "{$image}.{$width}.webp" : (defined('NO_WEBP') ? $image : "{$image}.webp");

    if (!file_exists($folder . $file)) {
        $img = @imagecreatefromstring(file_get_contents($folder . $image));

        if ($img) {
            if ($width) {
                $img = imagescale($img, $width);
            }

            imagewebp($img, $folder . $file);
            imagedestroy($img);
        } else {
            $file = $image;
        }
    }

    return $file;
};
