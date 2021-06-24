<?php //>

namespace matrix\web\backend;

class DeleteController extends Controller {

    public function __construct($name) {
        $this->values = [
            'confirmation' => cfg('backend.delete-confirmation'),
            'table' => table($name),
            'view' => 'backend/delete-success.php',
        ];
    }

    public function available() {
        if ($this->method() === 'POST') {
            $pattern = preg_quote($this->name(), '/');

            return preg_match("/^{$pattern}(\/[\/\w-]+)?$/", $this->path());
        }

        return false;
    }

    protected function process($form) {
        $args = $this->args();

        if (!$args) {
            $args = @$form['args'];

            if (!$args || !is_array($args)) {
                return ['error' => 'error.data-not-found'];
            }
        }

        if (!@$form['confirm']) {
            return ['args' => $args, 'view' => $this->confirmation()];
        }

        $list = [];

        foreach ($args as $id) {
            $data = $this->delete($this->table(), $id);

            if ($data === null) {
                break;
            }

            if ($data === false) {
                return ['error' => 'error.delete-failed'];
            }

            $list[] = $data;
        }

        if (!$list) {
            return ['error' => 'error.data-not-found'];
        }

        return ['success' => true, 'list' => $list];
    }

    private function delete($table, $data) {
        $data = $table->model()->delete($data);

        if ($data) {
            foreach ($table->getRelations() as $relation) {
                if ($relation['type'] === 'composition') {
                    $criterion = $relation['target']->equal($data[$relation['column']->name()]);
                    $foreign = $relation['foreign'];

                    foreach ($foreign->model()->query([$criterion]) as $child) {
                        $child = $this->delete($foreign, $child);

                        if (!$child) {
                            return $child;
                        }
                    }
                }
            }
        }

        return $data;
    }

}
