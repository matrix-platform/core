<?php //>

return function ($payload, $key = null, $iv = null, $safe = false) {
    return encrypt_data($payload, $key, $iv, $safe);
};
