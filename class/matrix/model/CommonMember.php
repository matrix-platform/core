<?php //>

namespace matrix\model;

use matrix\db\Model;

abstract class CommonMember extends Model {

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
            $curr['password'] = md5("{$curr['id']}::{$curr['password']}");
        }

        return $curr;
    }

    protected function queryValidMember($conditions) {
        $conditions['disabled'] = false;

        return $this->find($conditions);
    }

}
