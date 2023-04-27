<?php //>

return new class() extends matrix\web\Controller {

    public function __construct($values = []) {
        $this->values = ['view' => 'empty.php'];
    }

    public function available() {
        return ($this->method() === 'OPTIONS');
    }

    public function verify() {
        return true;
    }

    protected function process($form) {
        $this->response()->headers(['Access-Control-Allow-Headers' => '*', 'Access-Control-Allow-Origin' => '*']);

        return ['success' => true];
    }

};
