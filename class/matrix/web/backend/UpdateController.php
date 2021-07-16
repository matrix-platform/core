<?php //>

namespace matrix\web\backend;

use matrix\db\Transaction;

class UpdateController extends Controller {

    use Form, Transaction, Validator, Wrapper;

    public function __construct($name) {
        $this->values = [
            'columns' => false,
            'table' => table($name),
            'view' => 'backend/save-success.php',
        ];
    }

    public function available() {
        if ($this->method() === 'POST') {
            $pattern = preg_quote($this->name(), '/');

            return preg_match("/^{$pattern}\/[\w-]+$/", $this->path());
        }

        return false;
    }

    protected function wrap() {
        $this->formId($this->args()[0]);

        return $this->wrapModel(parent::wrap());
    }

    protected function process($form) {
        $form['id'] = $this->formId();

        $data = $this->table()->model()->update($form);

        if ($data === null) {
            return ['error' => 'error.data-not-found'];
        }

        if ($data === false) {
            if ($this->table()->versionable()) {
                return ['error' => 'error.data-outdated'];
            }

            return ['error' => 'error.update-failed'];
        }

        return $this->subprocess($form, ['success' => true, 'data' => $data]);
    }

    protected function subprocess($form, $result) {
        return $result;
    }

}
