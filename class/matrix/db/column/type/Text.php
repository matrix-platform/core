<?php //>

namespace matrix\db\column\type;

use PDO;

trait Text {

    public function convert($value) {
        return strval($value);
    }

    public function type() {
        return PDO::PARAM_STR;
    }

}
