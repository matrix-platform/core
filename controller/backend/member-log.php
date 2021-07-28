<?php //>

return new class('MemberLog') extends matrix\web\backend\ListController {

    protected function init() {
        $table = $this->table();

        $table->add('username', 'member.username');

        $this->columns('username', 'type', 'ip', 'create_time');
    }

};
