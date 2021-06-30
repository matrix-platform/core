<?php //>

return new Twig\TwigFunction('string', function ($value) {
    if ($value === null || is_string($value)) {
        return $value;
    }

    if (is_array($value)) {
        return implode(',', $value);
    }

    return var_export($value, true);
});
