<?php //>

namespace matrix\model;

use matrix\utility\Func;

trait CommonMember {

    public function queryById($id) {
        return $this->queryValidMember(['id' => $id]);
    }

    public function queryByUsername($username, $oauth = false) {
        $conditions = ['username' => $username];

        if ($oauth) {
            $conditions[] = $this->table->password->isNull();
        } else {
            $conditions[] = $this->table->password->notNull();
        }

        return $this->queryValidMember($conditions);
    }

    protected function before($type, $prev, $curr) {
        switch ($type) {
        case self::INSERT:
            $encrypt = isset($curr['password']);
            break;
        case self::UPDATE:
            if (isset($curr['password'])) {
                $encrypt = ($curr['password'] !== $prev['password']);
            } else {
                $curr['password'] = $prev['password'];
            }
            break;
        }

        if (@$encrypt) {
            $curr['password'] = Func::hash_password($curr);
        }

        return $curr;
    }

    protected function queryValidMember($conditions) {
        $today = date(cfg('system.date'));

        if (isset($this->table->begin_date)) {
            $conditions[] = $this->table->begin_date->notNull()->lessThanOrEqual($today);
        }

        if (isset($this->table->expire_date)) {
            $conditions[] = $this->table->expire_date->isNull()->or()->greaterThan($today);
        }

        $conditions['disabled'] = false;

        return $this->find($conditions);
    }

}
