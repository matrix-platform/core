<?php //>

namespace matrix\web;

use matrix\core\Handler;

trait RequestHandler {

    use Handler, Responsible, Verification;

    protected function cleanup() {
        Attachment::cleanup();
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
                if (is_string($data) && substr($data, 0, 5) === 'blob:') {
                    $data = $form[str_replace('.', '_', $data)][0]['path'];
                }

                $file = Attachment::from(@$filename[$idx], $data, @$description[$idx], $privilege);

                if ($file) {
                    $form[$name][$idx] = $file;
                }
            }
        } else {
            if (is_string($content) && substr($content, 0, 5) === 'blob:') {
                $content = $form[str_replace('.', '_', $content)][0]['path'];
            }

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
        $form = $this->jsonDecode(file_get_contents('php://input'));

        return is_array($form) ? $form : [];
    }

    protected function wrapPost() {
        $form = key_exists('JSON', $_POST) ? $this->jsonDecode($_POST['JSON']) : $_POST;

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

    private function jsonDecode($text) {
        $text = preg_replace_callback('/\\\\u([0-9a-f]{4})/i', fn ($m) => mb_convert_encoding(pack('H*', $m[1]), 'UTF-8', 'UCS-2BE'), $text);
        $text = preg_replace('/[^[:print:]]/u', '', $text);

        return json_decode($text, true);
    }

}
