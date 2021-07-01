<?php //>

namespace matrix\web\backend;

class GetController extends Controller {

    use Form;

    public function __construct($name) {
        $this->values = [
            'table' => table($name),
            'view' => 'backend/form.php',
        ];
    }

    public function available() {
        if ($this->method() === 'POST') {
            $pattern = preg_quote($this->name(), '/');

            return preg_match("/^{$pattern}[\w-]+$/", $this->path());
        }

        return false;
    }

    public function remix($styles, $list) {
        return $styles;
    }

    protected function process($form) {
        $model = $this->table()->model();
        $data = $model->get($this->args()[0]);

        if (!$data) {
            return ['error' => 'error.data-not-found'];
        }

        $data['.title'] = $model->toString($data);

        return $this->subprocess($form, ['success' => true, 'data' => $data]);
    }

    protected function subprocess($form, $result) {
        return $result;
    }

}
