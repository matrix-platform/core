<?php //>

return new Twig\TwigFilter('url', function ($path) {
    $protocol = $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
    $host = $_SERVER['HTTP_HOST'];

    return "{$protocol}{$host}{$path}";
});
