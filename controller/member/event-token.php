<?php //>

return new class() extends matrix\web\Controller {

    use matrix\web\MemberAware;

    protected function process($form) {
        $member = $this->member();

        if (!$member) {
            header('HTTP/1.1 404 Not Found');
            return ['view' => 'empty.php'];
        }

        $id = $member['id'];
        $timestamp = intval(microtime(true) * 1000) + cfg('system.event-secret-timeout');
        $secret = cfg('system.event-secret');
        $hash = strtoupper(hash('sha256', "{$id}:{$timestamp}:{$secret}"));

        return [
            'success' => true,
            'token' => "{$id}-{$timestamp}-{$hash}",
        ];
    }

};
