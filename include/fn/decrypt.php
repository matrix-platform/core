<?php //>

return function ($text, $key = null, $iv = null) {
    return decrypt_data($text, $key, $iv);
};
