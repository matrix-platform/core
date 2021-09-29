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

}
