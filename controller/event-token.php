<?php //>

return new class() extends matrix\web\Controller {

    use matrix\web\MemberAware;

    protected function process($form) {
        switch (@$form['type']) {
        case 'member':
            $member = $this->member();
            if ($member) {
                $id = $member['id'];
            }
            break;
        }

        if (empty($id)) {
            header('HTTP/1.1 404 Not Found');
            return ['view' => 'empty.php'];
        }

        $timestamp = intval(microtime(true) * 1000) + cfg('system.event-secret-timeout');
        $secret = cfg('system.event-secret');
        $hash = strtoupper(hash('sha256', "{$id}:{$timestamp}:{$secret}"));

        return [
            'success' => true,
            'token' => "{$id}-{$timestamp}-{$hash}",
        ];
    }

};
