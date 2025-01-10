<?php //>

use matrix\utility\Func;

return new Twig\TwigFilter('chinese', function ($input) {
    return Func::chinese_number($input);
});
