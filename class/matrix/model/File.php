<?php //>

namespace matrix\model;

use matrix\db\Model;

class File extends Model {

    protected function after($type, $prev, $curr) {
        switch ($type) {
        case self::UPDATE:
            $file = get_data_file("files/{$prev['path']}", false);
            if (is_file($file)) {
                unlink($file);
            }
            break;
        }
    }

}
