<?php //>

namespace matrix\web;

use matrix\core\Handler;

trait RequestHandler {

    use Handler, Responsible, Verification;

    protected function cleanup() {
        Attachment::cleanup();
    }

    protected function get($name) {
        return $this->initSession() ? @$_SESSION[$name] : null;
    }

    protected function remove($name) {
        if ($this->initSession() && key_exists($name, $_SESSION)) {
            unset($_SESSION[$name]);
        }
    }

    protected function set($name, $value) {
        if ($this->initSession(true)) {
            $_SESSION[$name] = $value;
        }
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

    protected function wrapFile($form, $name, $privilege = null) {
        $filename = @$form["{$name}#filename"];
        $content = @$form[$name];
        $description = @$form["{$name}#description"];

        if (is_array($content)) {
            foreach ($content as $idx => $data) {
                $file = Attachment::from(@$filename[$idx], $data, @$description[$idx], $privilege);

                if ($file) {
                    $form[$name][$idx] = $file;
                }
            }
        } else {
            $file = Attachment::from($filename, $content, $description, $privilege);

            if ($file) {
                $form[$name] = $file;
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

    private function initSession($force = false) {
        if (session_id()) {
            return true;
        }

        $exists = key_exists('matrix', $_COOKIE);

        if ($exists || $force) {
            if (!$exists) {
                session_set_cookie_params(['httponly' => true, 'path' => APP_PATH, 'samesite' => 'none', 'secure' => true]);
            }

            session_name('matrix');

            return session_start();
        }

        return false;
    }

}
