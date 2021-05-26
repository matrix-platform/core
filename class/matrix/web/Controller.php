<?php //>

namespace matrix\web;

use matrix\core\Controller as AbstractController;

class Controller extends AbstractController {

    public function available() {
        return ($this->method() === 'GET' && $this->name() === $this->path());
    }

    protected function destroy() {
        return session_destroy();
    }

    protected function get($name, $default = null) {
        return $_SESSION[$name] ?? $default;
    }

    protected function remove($name) {
        if (key_exists($name, $_SESSION)) {
            unset($_SESSION[$name]);
        }
    }

    protected function set($name, $value) {
        $_SESSION[$name] = $value;
    }

    protected function wrap() {
        switch ($this->method()) {
        case 'GET':
            return $this->wrapGet();

        case 'POST':
            if (preg_match('/application\/json/i', @$_SERVER['CONTENT_TYPE'])) {
                return array_replace($this->wrapGet(), $this->wrapJson());
            } else {
                return array_replace($this->wrapGet(), $this->wrapPost());
            }

        default:
            return [];
        }
    }

    protected function wrapGet() {
        return $_GET;
    }

    protected function wrapJson() {
        $form = json_decode(file_get_contents('php://input'), true);

        return is_array($form) ? $form : [];
    }

    protected function wrapPost() {
        $form = $_POST;

        foreach ($_FILES as $name => $value) {
            $files = [];

            if ($value['error'] === UPLOAD_ERR_OK) {
                $files[] = [
                    'name' => $value['name'],
                    'path' => $value['tmp_name'],
                ];
            } else if (is_array($value['error'])) {
                foreach ($value['error'] as $index => $error) {
                    if ($error === UPLOAD_ERR_OK) {
                        $files[] = [
                            'name' => $value['name'][$index],
                            'path' => $value['tmp_name'][$index],
                        ];
                    }
                }
            }

            $form[$name] = $files;
        }

        return $form;
    }

}
