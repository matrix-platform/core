<?php //>

return new class() extends matrix\web\backend\Controller {

    public function available() {
        if (in_array($this->method(), ['GET', 'POST'])) {
            $pattern = preg_quote($this->name(), '/');

            return preg_match("/^{$pattern}\/[\w-]+$/", $this->path());
        }

        return false;
    }

    protected function process($form) {
        $member = model('Member')->get($this->args()[0]);

        if (!$member) {
            return ['error' => 'error.data-not-found'];
        }

        $member['original_user'] = USER_ID;

        $this->set('Member', $member);

        //--

        $content = array_intersect_key($member, array_flip(['id', 'username']));

        model('UserLog')->insert([
            'user_id' => USER_ID,
            'type' => 5,
            'content' => json_encode($content, JSON_PRETTY_PRINT),
        ]);

        if ($this->method() === 'GET') {
            return ['success' => true, 'view' => '302.php', 'path' => APP_ROOT];
        } else {
            return ['success' => true, 'type' => 'open', 'path' => APP_ROOT];
        }
    }

};
