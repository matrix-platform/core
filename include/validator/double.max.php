<?php //>

return function ($value, $options) {
    if (key_exists('max', $options) && $options['max'] < $value) {
        return false;
    }

    return true;
};
