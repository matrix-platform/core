<?php //>

namespace matrix\model;

use matrix\core\AppException;
use matrix\db\Model;

class User extends Model {

    public function queryById($id) {
        return $this->queryValidUser(['id' => $id]);
    }

    public function queryByUsername($username) {
        return $this->queryValidUser(['username' => $username]);
    }

    protected function before($type, $prev, $curr) {
        switch ($type) {
        case self::DELETE:
            if ($prev['id'] <= 2) {
                throw new AppException('error.permission-denied');
            }
            break;
        case self::INSERT:
            if ($curr['id'] <= 2) {
                throw new AppException('error.permission-denied');
            }
            $encrypt = isset($curr['password']);
            break;
        case self::UPDATE:
            if ($prev['id'] <= 2 && $prev['id'] < constant('USER_ID')) {
                throw new AppException('error.permission-denied');
            }
            if (isset($curr['password'])) {
                $encrypt = ($curr['password'] !== $prev['password']);
            } else {
                $curr['password'] = $prev['password'];
            }
            break;
        }

        if (@$encrypt) {
            $curr['password'] = md5("{$curr['id']}::{$curr['password']}");
        }

        return $curr;
    }

    private function queryValidUser($conditions) {
        $today = date(cfg('system.date'));

        $conditions[] = $this->table->begin_date->notNull()->lessThanOrEqual($today);
        $conditions[] = $this->table->expire_date->isNull()->or()->greaterThan($today);

        $conditions['disabled'] = false;

        return $this->find($conditions);
    }

}
