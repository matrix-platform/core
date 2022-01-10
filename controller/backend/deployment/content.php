<?php //>

use matrix\web\backend\ListController;

return new class() extends matrix\web\backend\Controller {

    public function available() {
        if ($this->method() === 'POST') {
            $pattern = preg_quote($this->name(), '/');

            return preg_match("/^{$pattern}[\w-]+$/", $this->path());
        }

        return false;
    }

    protected function process($form) {
        $path = $this->args()[0];
        $controller = routing('/backend/' . base64_urldecode($path), 'POST');

        if ($controller instanceof ListController) {
            $conditions = [];
            $table = $controller->table();
            $relation = $table->getParentRelation();

            if ($relation) {
                $args = $controller->args();
                $conditions[$relation['column']->name()] = $args ? $args[0] : null;
            }

            $data = $table->model()->query($conditions);

            if (!$data) {
                return ['error' => 'error.data-not-found'];
            }

            return [
                'success' => true,
                'view' => 'backend/deployment.php',
                'controller' => $controller,
                'form' => $conditions,
                'data' => $data,
                'path' => $path,
            ];
        }

        return ['view' => '404.php'];
    }

};
