<?php //>

return function ($value, $options) {
    $pattern = @$options['pattern'];

    if ($pattern && preg_match($pattern, $value)) {
        return true;
    }

    return false;
};
