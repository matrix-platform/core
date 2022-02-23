<?php //>

return function ($args, $text) {
    $text = urlencode($text);

    @file_get_contents("https://api.telegram.org/bot{$args['bot']}/sendMessage?chat_id={$args['group']}&parse_mode=HTML&text={$text}");
};
