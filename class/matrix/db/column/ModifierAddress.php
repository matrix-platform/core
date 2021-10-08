<?php //>

namespace matrix\db\column;

class ModifierAddress {

    use type\Text;

    public function __construct($values) {
        $this->values = $values + [
            'blankStyle' => 'hidden',
            'disabled' => true,
            'formStyle' => 'text',
            'searchStyle' => 'like',
        ];
    }

    public function generate($value) {
        return constant('REMOTE_ADDR');
    }

    public function regenerate($value) {
        return $this->generate($value);
    }

}
