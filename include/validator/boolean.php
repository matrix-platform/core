<?php //>

return function ($value, $options) {
    if (filter_var($value, FILTER_VALIDATE_BOOLEAN, ['flags' => FILTER_NULL_ON_FAILURE]) !== null) {
        return true;
    }

    return false;
};
