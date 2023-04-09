<?php //>

return function ($id) {
    $timestamp = intval(microtime(true) * 1000) + cfg('system.event-secret-timeout');
    $secret = cfg('system.event-secret');
    $hash = strtoupper(hash('sha256', "{$id}:{$timestamp}:{$secret}"));

    return "{$id}-{$timestamp}-{$hash}";
};
