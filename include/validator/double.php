<?php //>

return function ($value, $options) {
    if (filter_var($value, FILTER_VALIDATE_FLOAT) !== false) {
        return true;
    }

    return false;
};
