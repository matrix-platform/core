<?php //>

namespace matrix\db\column;

class CreatorAddress {

    use type\Text;

    public function __construct($values) {
        $this->values = $values + [
            'blankStyle' => 'hidden',
            'formStyle' => 'text',
            'readonly' => true,
            'searchStyle' => 'like',
        ];
    }

    public function generate($value) {
        return constant('REMOTE_ADDR');
    }

}
