<?php //>

return new class() extends matrix\web\Controller {

    use matrix\web\VendorAware;

    protected function process($form) {
        $vendor = $this->vendor();

        if (!$vendor) {
            return ['view' => '404.php'];
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
