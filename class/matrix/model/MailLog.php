<?php //>

namespace matrix\model;

use matrix\db\Model;

class MailLog extends Model {

    protected function before($type, $prev, $curr) {
        switch ($type) {
        case self::INSERT:
            if ($curr['status'] === 0) {
                $curr['send_time'] = $curr['create_time'];
            }
            break;
        }

        return $curr;
    }

}
