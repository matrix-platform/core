<?php //>

namespace matrix\web\member;

trait OAuthLogin {

    use RememberMe;

    protected function initMember($member) {
    }

    private function login($data) {
        $model = model('Member');

        if (!$model->count(['username' => $data['username']])) {
            $data = $model->insert($data);

            $this->initMember($data);
        }

        $member = $model->queryByUsername($data['username'], true);

        if (!$member) {
            return ['error' => 'error.member-disabled'];
        }

        //--

        $this->set('Member', $member);

        model('MemberLog')->insert(['member_id' => $member['id'], 'type' => 1]);

        $this->forget();

        return [
            'success' => true,
            'view' => '302.php',
            'path' => $this->get('RETURN_PATH') ?: APP_ROOT,
        ];
    }

}
