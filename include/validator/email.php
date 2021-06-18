<?php //>

return function ($value, $options) {
    if (filter_var($value, FILTER_VALIDATE_EMAIL)) {
        return true;
    }

    return false;
};
