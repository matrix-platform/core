<?php //>

namespace matrix\model;

use matrix\db\Model;

class File extends Model {

    protected function after($type, $prev, $curr) {
        switch ($type) {
        case self::UPDATE:
            foreach (['id', 'path'] as $name) {
                $file = get_data_file("files/{$prev[$name]}");
                if ($file) {
                    unlink($file);
                }
            }
            break;
        }
    }

}
