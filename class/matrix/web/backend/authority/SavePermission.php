<?php //>

namespace matrix\web\backend\authority;

trait SavePermission {

    use BackupPermission;

    private function save($id, $data) {
        $permissions = [];

        foreach ($data as $perm) {
            if ($perm) {
                $path = strtok($perm, ':');
                $tag = strtok(':');

                $permissions[$path][$tag] = 1;
            }
        }

        $file = $this->backup($id);

        return file_put_contents($file, $permissions ? json_encode($permissions, JSON_PRETTY_PRINT) : '{}');
    }

}
