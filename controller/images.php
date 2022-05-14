<?php //>

use matrix\utility\Func;

return new class() extends matrix\web\Controller {

    public function available() {
        if ($this->method() === 'GET') {
            $pattern = preg_quote($this->name(), '/');

            return preg_match("/^{$pattern}(\/\d+)?(\/\d+)?\/[\w-]+$/", $this->path());
        }

        return false;
    }

    protected function process($form) {
        $args = $this->args();

        $path = array_pop($args);
        $file = base64_urldecode($path);

        if (cfg('system.image-optimization')) {
            $width = array_shift($args);
            $height = array_shift($args);

            $file = Func::optimize_image($file, intval($width), intval($height));
        }

        return ['view' => '302.php', 'path' => APP_PATH . 'files/' . $file];
    }

};
