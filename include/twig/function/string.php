<?php //>

return new Twig\TwigFunction('string', function ($value) {
    switch (gettype($value)) {
    case 'NULL':
        return $value;
    case 'array':
        return implode(',', $value);
    case 'double':
        $value = sprintf('%f', $value);
        return strpos($value, '.') === false ? $value : rtrim(rtrim($value, '0'), '.');
    case 'string':
        return $value;
    default:
        return var_export($value, true);
    }
});
