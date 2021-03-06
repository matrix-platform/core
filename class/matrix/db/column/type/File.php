<?php //>

namespace matrix\db\column\type;

use matrix\db\Column;
use matrix\web\Attachment;
use PDO;

trait File {

    use Column;

    public function convert($value) {
        $files = is_array($value) ? $value : [$value];

        foreach ($files as $file) {
            if ($file instanceof Attachment) {
                $file->save();
            }
        }

        return implode(',', $files);
    }

    public function type() {
        return PDO::PARAM_STR;
    }

}
