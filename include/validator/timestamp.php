<?php //>

return function ($value, $options) {
    $pattern = $options['pattern'];
    $datetime = DateTime::createFromFormat($pattern, $value);

    if ($datetime && $datetime->format($pattern) === $value) {
        return true;
    }

    return false;
};
