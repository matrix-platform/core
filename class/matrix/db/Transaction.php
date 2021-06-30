<?php //>

namespace matrix\db;

trait Transaction {

    protected function transaction() {
        return db();
    }

}
