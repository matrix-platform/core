<?php //>

namespace matrix\cli;

use matrix\utility\ValueObject;

class Response {

    use ValueObject;

    public function send() {
        $content = $this->content();

        if (strlen($content)) {
            echo $content;
            return;
        }

        $data = $this->json();

        if ($data !== null) {
            echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            return;
        }
    }

}
