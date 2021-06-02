<?php //>

namespace matrix\db\pgsql;

use matrix\db\Sequence as AbstractSequence;

class Sequence extends AbstractSequence {

    public function next($name) {
        $statement = $this->db->prepare("SELECT NEXTVAL('{$name}')");
        $statement->execute();

        return $statement->fetchColumn();
    }

}
