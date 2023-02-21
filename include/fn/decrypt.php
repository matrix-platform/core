<?php //>

return function ($text, $key) {
    $text = base64_urldecode($text);

    $hash = substr($text, -16);
    $text = substr($text, 0, -16);

    if ($hash !== hash('md5', $text, true)) {
        return false;
    }

    $cipher = 'AES-256-CBC';
    $length = openssl_cipher_iv_length($cipher);

    $iv = substr($text, 0, $length);
    $data = substr($text, $length);

    return openssl_decrypt($data, $cipher, $key, OPENSSL_RAW_DATA, $iv);
};
