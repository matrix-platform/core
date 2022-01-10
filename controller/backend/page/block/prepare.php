<?php //>

return new class('Block') extends matrix\web\backend\InsertController {

    protected function init() {
        $this->columns(['module']);
        $this->view(false);
    }

    protected function process($form) {
        $query = @$form['r'] ? "?r={$form['r']}" : '';

        return [
            'success' => true,
            'type' => 'redirect',
            'path' => "page/{$form['page_id']}/block/new/{$form['module']}{$query}",
        ];
    }

};
