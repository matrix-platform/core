<?php //>

return function ($text) {
    if (preg_match('/^[\d]{8}$/', $text)) {
        $sum1 = 0;
        $sum2 = 0;

        $codes = array_map('intval', str_split($text));
        $numbers = [1, 2, 1, 2, 1, 2, 4, 1];

        for ($i = 0; $i < 8; $i++) {
            $num = $codes[$i] * $numbers[$i];
            $num = intval($num / 10) + ($num % 10);

            if ($i === 6 && $codes[$i] === 7) {
                $sum1 += intval($num / 10);
                $sum2 += $num % 10;
            } else {
                $sum1 += $num;
                $sum2 += $num;
            }
        }

        return ($sum1 % 10 === 0) || ($sum2 % 10 === 0);
    }

    return false;
};
