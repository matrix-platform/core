<?php //>

use matrix\db\column\Boolean;
use matrix\db\column\Integer;
use matrix\db\column\Textarea;

return [

    'embedded-content.head-beginning' => Textarea::class,
    'embedded-content.head-ending' => Textarea::class,
    'embedded-content.body-beginning' => Textarea::class,
    'embedded-content.body-ending' => Textarea::class,

    'system.debug' => Boolean::class,
    'system.event-secret-timeout' => Integer::class,
    'system.sms-cooldown' => Integer::class,
    'system.verification-code-timeout' => Integer::class,

];
