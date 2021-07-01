<?php //>

return new class('Group') extends matrix\web\backend\UpdateController {

    use matrix\web\backend\authority\SavePermission;

    protected function subprocess($form, $result) {
        if (key_exists('permissions', $form)) {
            if ($this->save($result['data']['id'], $form['permissions']) === false) {
                return ['error' => 'error.update-failed'];
            }
        }

        return $result;
    }

};
