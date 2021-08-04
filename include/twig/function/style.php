<?php //>

return new Twig\TwigFunction('style', function ($values) {
    $styles = [];

    foreach ($values as $name => $value) {
        if (strlen($value)) {
            $name = strtolower(preg_replace('/([a-z\d])([A-Z])/', '$1-$2', $name));

            switch ($name) {
            case 'background-image':
                $styles[] = "{$name}:url({$value})";
                break;
            default:
                $styles[] = "{$name}:{$value}";
            }
        }
    }

    return implode(';', $styles);
});
