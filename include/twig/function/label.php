<?php //>

return new Twig\TwigFunction('label', function ($token, $default = null) {
    $text = i18n($token, $default);

    return new Twig\Markup("<span data-edit=\"{$token}\">{$text}</span>", 'UTF-8');
});
