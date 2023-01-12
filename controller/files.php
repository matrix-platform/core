<?php //>

return new class() extends matrix\web\Controller {

    use matrix\web\UserAware;

    public function available() {
        if ($this->method() === 'GET') {
            $pattern = preg_quote($this->name(), '/');

            return preg_match("/^{$pattern}\/[\w]+\/[\w.]+$/", $this->path());
        }

        return false;
    }

    protected function process($form) {
        $file = implode('/', $this->args());
        $data = load_file_data(preg_replace('/^([\w\/]+).*$/', '$1', $file));

        if ($data) {
            switch ($data['privilege']) {
            case 1:
                if (!$this->user() || !file_exists(PRIV_FILES_HOME . $file)) {
                    $data = null;
                }
                break;
            default:
                if (!file_exists(FILES_HOME . $file)) {
                    $data = null;
                }
            }
        }

        if (!$data) {
            return ['view' => '404.php'];
        }

        return ['success' => true, 'view' => 'file.php', 'data' => $data, 'file' => $file];
    }

};
