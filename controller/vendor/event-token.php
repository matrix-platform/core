<?php //>

return new class() extends matrix\web\Controller {

    use matrix\web\VendorAware;

    protected function process($form) {
        $vendor = $this->vendor();

        if (!$vendor) {
            header('HTTP/1.1 404 Not Found');
            return ['view' => 'empty.php'];
        }

        $id = $vendor['id'];
        $timestamp = intval(microtime(true) * 1000) + cfg('system.event-secret-timeout');
        $secret = cfg('system.event-secret');
        $hash = strtoupper(hash('sha256', "{$id}:{$timestamp}:{$secret}"));

        return [
            'success' => true,
            'token' => "{$id}-{$timestamp}-{$hash}",
        ];
    }

};
