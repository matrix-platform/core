<?php //>

return [

    'method' => 'mitake',

    'key' => null,

    'prefix' => null,

    'screct' => null,

    'url' => 'http://smsapi.mitake.com.tw/api/mtk/SmSend?username={{ key }}&password={{ screct }}&dstaddr={{ mobile }}&smbody={{ text|url_encode }}&CharsetURL=UTF-8',

    'safe-point' => 30,

];
