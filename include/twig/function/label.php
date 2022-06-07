<?php //>

return new Twig\TwigFunction('label', function ($token, $default = null, $data = null) {
    $text = i18n($token, $default);

    if ($data) {
        $text = render($text, $data);
    }

    return new Twig\Markup("<span data-edit=\"{$token}\">{$text}</span>", 'UTF-8');
});
