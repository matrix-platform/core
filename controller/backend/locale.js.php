<?php //>

return new class() extends matrix\web\Controller {

    protected function init() {
        $this->view('backend/locale.js.twig');
    }

    protected function process($form) {
        return [
            'success' => true,
            'locale' => load_i18n('backend-locale'),
        ];
    }

};
