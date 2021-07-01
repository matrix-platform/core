<?php //>

return new class('User') extends matrix\web\backend\GetController {

    use matrix\web\backend\authority\PermissionTree;

    protected function subprocess($form, $result) {
        if ($result['data']['id'] === 1 && USER_ID !== 1) {
            return ['error' => 'error.data-not-found'];
        }

        return $result;
    }

    protected function postprocess($form, $result) {
        $result['data']['permissions'] = load_data("permission/User/{$result['data']['id']}");

        unset($result['data']['password']);

        return $result;
    }

};
