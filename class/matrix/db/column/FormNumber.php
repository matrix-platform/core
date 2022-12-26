<?php //>

namespace matrix\db\column;

class FormNumber {

    use type\Text;

    const RESET_NONE = 0;
    const RESET_DAILY = 1;
    const RESET_MONTHLY = 3;
    const RESET_YEARLY = 5;

    public function __construct($values) {
        $this->values = $values + [
            'formStyle' => 'text',
            'length' => 3,
            'pattern' => 'Ymd',
            'reset' => self::RESET_DAILY,
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
