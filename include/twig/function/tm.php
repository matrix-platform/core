<?php //>

return new Twig\TwigFunction('tm', function ($name, $data = null) {
    return new matrix\view\twig\I18nText($name, $data, true);
});
