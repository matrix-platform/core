<?php //>

return new class() extends matrix\web\Controller {

    public function available() {
        if ($this->method() === 'GET') {
            $pattern = preg_quote($this->name(), '/');

            return preg_match("/^{$pattern}\/[\w][.\/\w-]+$/", $this->path());
        }

        return false;
    }

    protected function process($form) {
        $file = implode('/', $this->args());

        if (is_file(APP_HOME . 'www/files/' . $file)) {
            return ['view' => '302.php', 'path' => APP_PATH . 'files/' . $file];
        }

        return ['view' => '404.php'];
    }

};
