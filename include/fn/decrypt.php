<?php //>

return function ($data, $key) {
    list($text, $iv, $tag) = explode(':', base64_urldecode($data));

    return openssl_decrypt($text, 'aes-128-gcm', $key, 0, base64_decode($iv), base64_decode($tag));
};
