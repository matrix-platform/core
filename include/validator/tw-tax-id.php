<?php //>

use matrix\utility\Func;

return function ($value, $options) {
    return Func::is_tw_tax_id($value);
};
