<?php //>

return new class('Menu') extends matrix\web\backend\ListController {

    protected function init() {
        $table = $this->table();

        $table->add('item_count', 'item.count');

        $this->columns($table->getColumns([
            'title',
            'icon',
            'url',
            'item_count',
        ]));
    }

};
