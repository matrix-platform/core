<?php //>

return function ($value, $options) {
    if (filter_var($value, FILTER_VALIDATE_IP)) {
        return true;
    }

    return false;
};
