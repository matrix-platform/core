<?php //>

namespace matrix\web;

use matrix\utility\ValueObject;

class Response {

    use ValueObject;

    public function send() {
        $status = $this->status();

        if ($status) {
            $text = cfg("http-status.{$status}") ?: 'unknown status';

            header("HTTP/1.1 {$status} {$text}");
        }

        $headers = $this->headers();

        if ($headers) {
            foreach ($headers as $name => $value) {
                header("{$name}: {$value}");
            }
        }

        $content = $this->content();

        if (strlen($content)) {
            echo $content;
            return;
        }

        $data = $this->json();

        if ($data !== null) {
            header('Content-Type: application/json; charset=UTF-8');
            echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            return;
        }

        $path = $this->file();

        if ($path) {
            readfile($path);
            return;
        }

        $location = $this->redirect();

        if ($location) {
            header("Location: {$location}");
            return;
        }
    }

}
