<?php //>

namespace matrix\cli;

use matrix\core\Controller as AbstractController;

class Controller extends AbstractController {

    public function __construct($values = []) {
        $values['view'] = $values['view'] ?? 'empty.php';

        parent::__construct($values);
    }

    public function available() {
        return ($this->method() === 'cli');
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
