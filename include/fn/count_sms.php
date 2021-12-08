<?php //>

return function ($conditions, $seconds) {
    $table = table('SmsLog');
    $timestamp = date(cfg('system.timestamp'), time() - $seconds);

    if (!is_array($conditions)) {
        $conditions = ['ip' => $conditions];
    }

    $conditions[] = $table->create_time->GreaterThan($timestamp);

    return $table->model()->count($conditions);
};
