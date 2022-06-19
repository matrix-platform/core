<?php //>

use matrix\db\column\Boolean;
use matrix\db\column\Integer;
use matrix\db\column\Textarea;

return [

    'embedded-content.head-beginning' => Textarea::class,
    'embedded-content.head-ending' => Textarea::class,
    'embedded-content.body-beginning' => Textarea::class,
    'embedded-content.body-ending' => Textarea::class,

    'security.ip-count' => Integer::class,
    'security.ip-seconds' => Integer::class,
    'security.member-count' => Integer::class,
    'security.member-seconds' => Integer::class,

    'sms.safe-point' => Integer::class,

    'system.debug' => Boolean::class,
    'system.event-secret-timeout' => Integer::class,
    'system.sms-cooldown' => Integer::class,
    'system.verification-code-timeout' => Integer::class,

];
