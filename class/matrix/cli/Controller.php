<?php //>

namespace matrix\cli;

use matrix\core\Handler;

class Controller {

    use Handler;

    public function __construct($values = []) {
        $this->values = $values + ['view' => 'empty.php'];
    }

    public function available() {
        if ($this->method() === 'cli') {
            $pattern = preg_quote($this->name(), '/');

            return preg_match("/^{$pattern}(\/.+)?$/", $this->path());
        }

        return false;
    }

    public function execute() {
        $this->handle();
    }

    protected function wrap() {
        $form = [];

        foreach (array_splice($_SERVER['argv'], 2) as $arg) {
            $tokens = array_map('trim', preg_split('/=/', $arg, 2));

            $form[$tokens[0]] = count($tokens) > 1 ? $tokens[1] : true;
        }

        return $form;
    }

}
