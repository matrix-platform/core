<?php //>

return new class('File') extends matrix\web\backend\UpdateController {

    protected function init() {
        $table = $this->table();

        $table->parent_id->readonly(true);
        $table->privilege->readonly(true);
        $table->owner_id->readonly(true);
        $table->group_id->readonly(true);
        $table->deleted->readonly(true);

        $this->view('backend/save-file-info-success.php');
    }

};
