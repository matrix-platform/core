<?php //>

return function ($value, $options) {
    if (preg_match("/{$options['pattern']}/", $value)) {
        return true;
    }

    return false;
};
