<?php //>

namespace matrix\web\backend;

class GetController extends Controller {

    use Form, FormRemixer;

    public function __construct($name) {
        $this->values = [
            'table' => table($name),
            'view' => 'backend/form.php',
        ];
    }

    public function available() {
        if ($this->method() === 'POST') {
            $pattern = preg_quote($this->name(), '/');

            return preg_match("/^{$pattern}[\w-]+(\/history)?$/", $this->path());
        }

        return false;
    }

    protected function process($form) {
        $args = $this->args();
        $table = $this->table();
        $model = $table->model();
        $data = $model->get($args[0]);

        if (!$data) {
            return ['error' => 'error.data-not-found'];
        }

        $history = null;

        if (@$args[1] === 'history') {
            $history = [];

            foreach ($model->history($data['id']) as $row) {
                switch ($row['controller']) {
                case '/backend/deployment/update':
                    $row['type'] = 6;
                    break;
                case '/backend/set-visible':
                    $row['type'] = @$row['current']['enable_time'] ? 4 : 5;
                    break;
                }

                $history[] = $row;
            }

            if (!$history) {
                return ['error' => 'error.data-not-found'];
            }

            $this->view('backend/history.php');
        } else {
            $sublist = $this->sublist();

            if ($sublist) {
                foreach ($table->getRelations() as $alias => $relation) {
                    $node = "{$this->node()}{$alias}";

                    if ($relation['type'] === 'composition' && !$relation['junction'] && in_array($node, $sublist)) {
                        $id = $data[$relation['column']->name()];
                        $data["{$node}:count"] = $relation['foreign']->filter($relation['target']->equal($id))->count();
                    }
                }
            }
        }

        $data['.title'] = $model->toString($data);

        return $this->subprocess($form, ['success' => true, 'data' => $data, 'history' => $history]);
    }

}
