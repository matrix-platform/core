<?php //>

namespace matrix\web;

trait MemberAware {

    use member\RememberMe;

    private $member;

    public function member() {
        if ($this->member === null) {
            $this->member = false;

            $member = $this->get('Member') ?: $this->recall();

            if ($member) {
                $current = model('Member')->queryById($member['id']);

                if ($current && $current['password'] === $member['password']) {
                    $this->member = $current;

                    $this->set('Member', $current);
                } else {
                    $this->remove('Member');
                }
            }
        }

        return $this->member;
    }

}
