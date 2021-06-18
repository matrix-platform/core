<?php //>

return function ($value, $options) {
    if (filter_var($value, FILTER_VALIDATE_INT) !== false) {
        return true;
    }

    return false;
};
