<?php //>

return new Twig\TwigFunction('t', function ($name, $data = null) {
    return new matrix\view\twig\I18nText($name, $data, false);
});
