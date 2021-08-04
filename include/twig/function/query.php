<?php //>

return new Twig\TwigFunction('query', function ($name, $conditions = [], $size = 0) {
    return model($name)->query($conditions, true, $size);
});
