<?php //>

return new class('Group') extends matrix\web\backend\GetController {

    use matrix\web\backend\authority\PermissionTree;

    protected function postprocess($form, $result) {
        $result['data']['permissions'] = load_data("permission/Group/{$result['data']['id']}");

        return $result;
    }

};
