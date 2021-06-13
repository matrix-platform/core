<?php //>

namespace matrix\db\column;

use matrix\db\Column;

class FormNumber {

    use Column, type\Text;

    public function __construct($values) {
        $this->values = $values + [
            'formStyle' => 'text',
            'length' => 3,
            'pattern' => 'Ymd',
            'searchStyle' => 'like',
        ];
    }

    public function generate($value) {
        if ($value === null) {
            $sequence = db()->next($this->sequence());

            $date = date($this->pattern());
            $number = str_pad($sequence, $this->length(), '0', STR_PAD_LEFT);

            return "{$this->prefix()}{$date}{$number}";
        }

        return $value;
    }

}
