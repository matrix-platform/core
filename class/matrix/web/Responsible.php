<?php //>

namespace matrix\web;

trait Responsible {

    private $response;

    public function response() {
        if ($this->response === null) {
            $this->response = new Response();
        }

        return $this->response;
    }

}
