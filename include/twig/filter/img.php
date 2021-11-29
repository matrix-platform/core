<?php //>

use matrix\utility\Func;

return new Twig\TwigFilter('img', function ($image, $width = 0, $height = 0) {
    if (!$image || preg_match('/^data:/', $image)) {
        return $image;
    }

    if (defined('NO_WEBP')) {
        return APP_PATH . 'images/' . $width . '/' . $height . '/' . base64_urlencode($image);
    } else {
        return APP_PATH . 'files/' . Func::optimize_image($image, $width, $height);
    }
});
