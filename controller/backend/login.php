<?php //>

return new class() extends matrix\web\Controller {

    use matrix\web\UserAware;

    public function available() {
        if ($this->method() === 'GET') {
            $pattern = preg_quote($this->name(), '/');

            return preg_match("/^{$pattern}(\/[\w-]+)?$/", $this->path());
        }

        return false;
    }

    protected function init() {
        $this->view(cfg('backend.login-form'));
    }

    protected function process($form) {
        $args = $this->args();
        $path = $args ? base64_urldecode($args[0]) : (APP_ROOT . 'backend/');

        $result = ['success' => true, 'path' => $path];

        if ($this->user()) {
            $result['view'] = '302.php';
        }

        return $result;
    }

};
