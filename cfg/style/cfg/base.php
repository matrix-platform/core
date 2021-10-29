<?php //>

use matrix\db\column\Boolean;
use matrix\db\column\Integer;

return [

    'system.debug' => Boolean::class,
    'system.event-secret-timeout' => Integer::class,
    'system.sms-cooldown' => Integer::class,
    'system.verification-code-timeout' => Integer::class,

];
