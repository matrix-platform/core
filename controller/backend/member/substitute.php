<?php //>

return new class() extends matrix\web\backend\Controller {

    public function available() {
        if ($this->method() === 'POST') {
            $pattern = preg_quote($this->name(), '/');

            return preg_match("/^{$pattern}\/[\w-]+$/", $this->path());
        }

        return false;
    }

    protected function process($form) {
        $member = model('Member')->queryById($this->args()[0]);

        if (!$member) {
            return ['error' => 'error.data-not-found'];
        }

        $this->set('Member', $member);

        return [
            'success' => true,
            'type' => 'open',
            'path' => APP_ROOT,
        ];
    }

};
