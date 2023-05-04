<?php //>

return function ($payload, $key = null) {
    static $iv;

    $cipher = 'AES-256-CBC';

    if ($iv === null) {
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($cipher));
    }

    $data = openssl_encrypt($payload, $cipher, $key ?: cfg('system.default-key'), OPENSSL_RAW_DATA, $iv);

    $text = $iv . $data;
    $hash = hash('md5', $text, true);

    return base64_urlencode($text . $hash);
};
