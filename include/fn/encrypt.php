<?php //>

return function ($payload, $key) {
    $cipher = 'aes-128-gcm';
    $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($cipher));
    $text = openssl_encrypt($payload, $cipher, $key, 0, $iv, $tag);

    return base64_urlencode($text . ':' . base64_encode($iv) . ':' . base64_encode($tag));
};
