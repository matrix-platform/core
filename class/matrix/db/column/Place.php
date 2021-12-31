<?php //>

namespace matrix\db\column;

class Place {

    use type\Text;

    public function __construct($values) {
        $this->values = $values + [
            'formStyle' => 'place',
            'searchStyle' => 'like',
        ];
    }

}
