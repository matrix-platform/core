<?php //>

return new class('Page') extends matrix\web\backend\ListController {

    protected function init() {
        $table = $this->table();

        $table->add('block_count', 'block.count');

        $this->columns('path', 'title', 'block_count');
    }

};
