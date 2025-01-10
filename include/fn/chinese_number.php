<?php //>

$digits = ['零', '一', '二', '三', '四', '五', '六', '七', '八', '九'];
$units = ['', '十', '百', '千', '萬', '十', '百', '千', '億', '十', '百', '千', '兆', '十', '百', '千', '京', '十', '百', '千', '垓'];

return function ($input) use ($digits, $units) {
    $value = intval($input);

    if (!$value) {
        return $digits[0];
    }

    $num = abs($value);
    $text = '';

    for ($i = 0; $num > 0; $i++) {
        $text = "{$digits[$num % 10]}{$units[$i]}{$text}";
        $num = floor($num / 10);
    }

    $text = preg_replace('/(零)[十百千]/u', '$1', $text);
    $text = preg_replace('/([萬億兆京垓])[^十百千]+([萬億兆京垓])/u', '$1零', $text);
    $text = preg_replace('/([十百千])零+([萬億兆京垓])/u', '$1$2零', $text);
    $text = preg_replace('/(零)+/u', '$1', $text);
    $text = preg_replace('/零+$/u', '', $text);

    return ($value < 0) ? "負{$text}" : $text;
};
