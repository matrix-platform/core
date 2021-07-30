<?php //>

return [

    'method' => 'mitake',

    'key' => '',

    'prefix' => '',

    'screct' => '',

    'url' => 'http://smsapi.mitake.com.tw/api/mtk/SmSend?username={{ key }}&password={{ screct }}&dstaddr={{ mobile }}&smbody={{ text|url_encode }}&CharsetURL=UTF-8',

];
