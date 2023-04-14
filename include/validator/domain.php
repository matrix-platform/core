<?php //>

return function ($value, $options) {
    return checkdnsrr($value, 'ANY');
};
