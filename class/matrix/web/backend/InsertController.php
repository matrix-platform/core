<?php //>

namespace matrix\web\backend;

class InsertController extends Controller {

    use BlankForm, Validator, Wrapper;

    public function __construct($name) {
        $this->values = [
            'table' => table($name),
            'view' => 'backend/save-success.php',
        ];
    }

    protected function wrap() {
        return $this->wrapParentId($this->wrapModel(parent::wrap()));
    }

    protected function process($form) {
        $data = $this->table()->model()->insert($form);

        if (!$data) {
            return ['error' => 'error.insert-failed'];
        }

        return ['success' => true, 'data' => $data];
    }

}