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
        $folder = defined('FILES_HOME') ? FILES_HOME : (APP_HOME . 'www/files/');
        $file = implode('/', $this->args());

        if (is_file($folder . $file)) {
            $root = defined('FILES_HOME') ? '/' : APP_PATH;

            return ['view' => '302.php', 'path' => "{$root}files/{$file}"];
        }

        return ['view' => '404.php'];
    }

};
