<?php //>

namespace matrix\web\backend;

class InsertController extends Controller {

    use BlankForm, Validator, Wrapper;

    public function __construct($name, $deepClone = true) {
        $this->values = [
            'columns' => false,
            'deepClone' => $deepClone,
            'table' => table($name),
            'view' => 'backend/save-success.php',
        ];
    }

    protected function wrap() {
        return $this->wrapParentId($this->wrapModel(parent::wrap()));
    }

    protected function process($form) {
        $table = $this->table();
        $model = $table->model();

        $data = $model->insert($form);

        if (!$data) {
            return ['error' => 'error.insert-failed'];
        }

        if ($this->deepClone()) {
            $args = $this->args();

            if ($table->getParentRelation()) {
                array_shift($args);
            }

            if ($args) {
                $origin = $model->get($args[0]);

                if ($origin) {
                    $this->cloneChildren($table, $origin, $data['id']);
                }
            }
        }

        return $this->subprocess($form, ['success' => true, 'data' => $data]);
    }

    private function cloneChildren($table, $parent, $newParentId) {
        foreach ($table->getRelations() as $relation) {
            if ($relation['type'] === 'composition' && $relation['foreign']->cloneable()) {
                $foreign = $relation['foreign'];
                $column = $relation['target'];

                $criterion = $column->equal($parent[$relation['column']->name()]);

                foreach ($foreign->model()->query([$criterion]) as $origin) {
                    $child = $origin;
                    $child[$column->name()] = $newParentId;

                    unset($child['id']);

                    $child = $foreign->model()->insert($child);

                    if ($child) {
                        $this->cloneChildren($foreign, $origin, $child['id']);
                    }
                }
            }
        }
    }

}
