<?php //>

namespace matrix\web\backend;

class BlankController extends Controller {

    use BlankForm, FormRemixer;

    public function __construct($name) {
        $this->values = [
            'table' => table($name),
            'view' => 'backend/blank.php',
        ];
    }

    protected function wrap() {
        return $this->wrapParentId(parent::wrap());
    }

    protected function process($form) {
        $data = null;
        $table = $this->table();

        if ($table->cloneable()) {
            $args = $this->args();

            if ($this->getParentRelation()) {
                array_shift($args);
            }

            if ($args) {
                $model = $table->model();
                $data = $model->get($args[0]);

                if ($data) {
                    foreach (['enableTime', 'disableTime', 'ranking'] as $name) {
                        $column = $table->{$name}();

                        if ($column) {
                            unset($data[$column]);
                        }
                    }

                    $data['.title'] = $model->toString($data);
                }
            }
        }

        return $this->subprocess($form, ['success' => true, 'data' => $data ?: $form]);
    }

}
