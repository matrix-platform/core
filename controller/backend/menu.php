<?php //>

return new class('Menu') extends matrix\web\backend\ListController {

    protected function init() {
        $table = $this->table();

        $table->add('item_count', 'item.count');

        $this->columns('title', 'url', 'item_count');
    }

};
