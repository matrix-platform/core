<?php //>

return new Twig\TwigFunction('string', function ($value) {
    return $value === null || is_string($value) ? $value : var_export($value, true);
});
