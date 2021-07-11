<?php //>

namespace matrix\web;

use matrix\core\Handler;

trait RequestHandler {

    use Handler;

    public function verify() {
        if ($this->method() === 'POST') {
            $token = @$_SERVER['HTTP_MATRIX_TOKEN'];

            return $token ? ($token === @$_COOKIE['matrix-token']) : false;
        }

        return true;
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

    protected function cleanup() {
        Attachment::cleanup();
    }

    protected function wrapFile($form, ...$names) {
        foreach ($names as $name) {
            $filename = @$form["{$name}#filename"];
            $content = @$form[$name];
            $description = @$form["{$name}#description"];

            if (is_array($content)) {
                foreach ($content as $idx => $data) {
                    $file = Attachment::from(@$filename[$idx], $data, @$description[$idx]);

                    if ($file) {
                        $form[$name][$idx] = $file;
                    }
                }
            } else {
                $file = Attachment::from($filename, $content, $description);

                if ($file) {
                    $form[$name] = $file;
                }
            }
        }

        return $form;
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
