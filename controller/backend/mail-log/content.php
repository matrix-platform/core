<?php //>

return new class('MailLog') extends matrix\web\backend\GetController {

    protected function postprocess($form, $result) {
        $data = $result['data'];
        $table = $this->table();

        if ($data['create_time'] === $data['send_time']) {
            $table->create_time->invisible(true);
        }

        if ($data['send_time'] === null) {
            $table->send_time->invisible(true);
        }

        if ($data['status'] === 1) {
            $table->sender->invisible(true);
        }

        return $result;
    }

};
