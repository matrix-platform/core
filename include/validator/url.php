<?php //>

return function ($value, $options) {
    if (filter_var($value, FILTER_VALIDATE_URL)) {
        return true;
    }

    return false;
};
