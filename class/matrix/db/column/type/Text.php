<?php //>

namespace matrix\db\column\type;

use matrix\db\Column;
use PDO;

trait Text {

    use Column;

    public function convert($value) {
        return strval($value);
    }

    public function type() {
        return PDO::PARAM_STR;
    }

}
