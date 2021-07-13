<?php //>

return new Twig\TwigFilter('youtube', function ($url) {
    $tokens = parse_url($url);

    parse_str(@$tokens['query'], $query);

    $id = @$query['v'] ?: substr(@$tokens['path'], 1);

    return "https://www.youtube.com/embed/{$id}";
});
