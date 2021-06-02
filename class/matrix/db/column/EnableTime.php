<?php //>

namespace matrix\db\column;

class EnableTime extends Timestamp {

    public function __construct($values) {
        parent::__construct($values);

        $this->table()->enableTime($this->name());
    }

}
