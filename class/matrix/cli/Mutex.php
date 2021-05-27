<?php //>

namespace matrix\cli;

trait Mutex {

    public function execute() {
        $name = str_replace('/', '.', $this->name());
        $file = fopen(create_folder(APP_DATA) . $name, 'w');

        if (flock($file, LOCK_EX | LOCK_NB)) {
            parent::execute();
        }
    }

}
