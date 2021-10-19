<?php //>

return new class() extends matrix\web\Controller {

    public function available() {
        if ($this->method() === 'GET') {
            $pattern = preg_quote($this->name(), '/');

            return preg_match("/^{$pattern}\/[\w]+\/[\w.]+$/", $this->path());
        }

        return false;
    }

    protected function process($form) {
        $path = APP_PATH . 'files/' . implode('/', $this->args());

        return ['view' => '302.php', 'path' => $path];
    }

};
