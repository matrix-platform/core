<?php //>

return [

    'mailer' => 'gmail',

    'to' => null,

    'subject' => 'SMS quota is insufficient',

    'content' => 'The current limit {{ current }} is lower than the safe value {{ safe }}, please recharge as soon as possible.',

];
