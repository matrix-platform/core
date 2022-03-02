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
        $vendor = model('Vendor')->get($this->args()[0]);

        if (!$vendor) {
            return ['error' => 'error.data-not-found'];
        }

        $vendor['original_user'] = USER_ID;

        $this->set('Vendor', $vendor);

        //--

        $content = array_intersect_key($vendor, array_flip(['id', 'username']));

        model('UserLog')->insert([
            'user_id' => USER_ID,
            'type' => 6,
            'content' => json_encode($content, JSON_PRETTY_PRINT),
        ]);

        return [
            'success' => true,
            'type' => 'open',
            'path' => APP_ROOT . 'vendor/',
        ];
    }

};
