<?php //>

return new class('Group') extends matrix\web\backend\DeleteController {

    use matrix\web\backend\authority\BackupPermission;

    protected function subprocess($form, $result) {
        array_map([$this, 'backup'], $result['list']);

        return $result;
    }

};
