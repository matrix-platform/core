<?php //>

return function ($payload, $key = null, $iv = null) {
    return encrypt_data($payload, $key, $iv);
};
