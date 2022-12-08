<?php //>

return function ($text) {
    if (preg_match('/^[a-zA-Z][1-2][\d]{8}$/', $text)) {
        $codes = str_split($text);
        $numbers = [0, 8, 7, 6, 5, 4, 3, 2, 1, 1];

        $sum = [
            'A' => 1, 'B' => 0, 'C' => 9, 'D' => 8, 'E' => 7, 'F' => 6, 'G' => 5, 'H' => 4, 'I' => 9, 'J' => 3,
            'K' => 2, 'L' => 2, 'M' => 1, 'N' => 0, 'O' => 8, 'P' => 9, 'Q' => 8, 'R' => 7, 'S' => 6, 'T' => 5,
            'U' => 4, 'V' => 3, 'W' => 1, 'X' => 3, 'Y' => 2, 'Z' => 0,
        ][strtoupper($codes[0])];

        for ($i = 1; $i < 10; $i++) {
            $sum += $codes[$i] * $numbers[$i];
        }

        return ($sum % 10 === 0);
    }

    return false;
};
