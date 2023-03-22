<?php //>

return function ($payload, $key = null) {
    $cipher = 'AES-256-CBC';
    $length = openssl_cipher_iv_length($cipher);

    $iv = openssl_random_pseudo_bytes($length);
    $data = openssl_encrypt($payload, $cipher, $key ?: cfg('system.default-key'), OPENSSL_RAW_DATA, $iv);

    $text = $iv . $data;
    $hash = hash('md5', $text, true);

    return base64_urlencode($text . $hash);
};
