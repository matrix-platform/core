<?php //>

return function ($text) {
    preg_match_all(cfg('frontend.url-pattern'), $text, $links);

    return array_values(array_unique(array_map('html_entity_decode', $links[0])));
};
