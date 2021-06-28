<?php //>

use matrix\utility\Fn;

return new Twig\TwigFilter('img', function ($image, $width = 0) {
    if (!$image || preg_match('/^data:/', $image)) {
        return $image;
    }

    return (defined('FILES_HOME') ? '/' : APP_PATH) . 'files/' . Fn::optimize_image($image, $width);
});
