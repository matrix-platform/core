<?php //>

namespace matrix\web\backend\authority;

trait BackupPermission {

    private function backup($data) {
        $id = is_array($data) ? $data['id'] : $data;
        $file = create_folder(APP_DATA . "permission/{$this->table()->name()}/") . $id;

        if (file_exists($file)) {
            $info = pathinfo($file);

            rename($file, "{$info['dirname']}/.{$info['basename']}-" . microtime(true));
        }

        return $file;
    }

}
