<?php //>

namespace matrix\cli;

trait Mutex {

    public function execute() {
        $file = fopen(APP_DATA . str_replace('/', '.', $this->name()), 'w');

        if (flock($file, LOCK_EX | LOCK_NB)) {
            parent::execute();
        }
    }

}
