<?php //>

// php www/index.php /console/init-rsa-keys

return new class() extends matrix\cli\Controller {

    protected function process($form) {
        $file = APP_DATA . 'rsa-private-key';

        if (!file_exists($file)) {
            $key = openssl_pkey_new(["private_key_bits" => 4096, "private_key_type" => OPENSSL_KEYTYPE_RSA]);

            openssl_pkey_export($key, $private);

            $public = openssl_pkey_get_details($key);

            file_put_contents(FILES_HOME . 'rsa-public-key', $public['key']);
            file_put_contents($file, $private);
        }

        return ['success' => true];
    }

};
