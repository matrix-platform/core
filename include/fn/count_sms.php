<?php //>

return function ($ip, $seconds) {
    $table = table('SmsLog');
    $timestamp = date(cfg('system.timestamp'), time() - $seconds);

    return $table->model()->count(['ip' => $ip, $table->create_time->GreaterThan($timestamp)]);
};
