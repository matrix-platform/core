<?php //>

namespace matrix\web\backend;

class BlankController extends Controller {

    use BlankForm;

    public function __construct($name) {
        $this->values = [
            'table' => table($name),
            'view' => 'backend/blank.php',
        ];
    }

    public function remix($styles) {
        return $styles;
    }

    protected function wrap() {
        return $this->wrapParentId(parent::wrap());
    }

}
