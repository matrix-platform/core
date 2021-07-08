<?php //>

return new class('Block') extends matrix\web\backend\InsertController {

    protected function init() {
        $this->columns($this->table()->getColumns([
            'module',
        ]));
    }

    protected function process($form) {
        $query = @$form['r'] ? "?r={$form['r']}" : '';

        return [
            'success' => true,
            'view' => 'raw.php',
            'type' => 'redirect',
            'path' => "page/{$form['page_id']}/block/new/{$form['module']}{$query}",
        ];
    }

};
