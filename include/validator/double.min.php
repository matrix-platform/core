<?php //>

return function ($value, $options) {
    if (key_exists('min', $options) && $options['min'] > $value) {
        return false;
    }

    return true;
};
