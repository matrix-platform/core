<?php //>

return new Twig\TwigFilter('file', function ($file) {
    if (!$file || preg_match('/^data:/', $file)) {
        return $file;
    }

    return APP_PATH . 'files/' . $file;
});
