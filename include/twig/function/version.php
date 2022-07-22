<?php //>

return new Twig\TwigFunction('version', function () {
    static $version;

    if ($version === null) {
        $version = trim(@file_get_contents(APP_HOME . 'version')) ?: time();
    }

    return $version;
});
