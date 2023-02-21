<?php //>

return new class('MailLog') extends matrix\web\backend\GetController {

    protected function postprocess($form, $result) {
        $data = $result['data'];

        $table = $this->table();
        $table->mailer->invisible(true);

        if ($data['create_time'] === $data['send_time']) {
            $table->create_time->invisible(true);
        }

        if ($data['send_time'] === null) {
            $table->send_time->invisible(true);
        }

        if ($data['status'] === 1) {
            $table->sender->invisible(true);
        } else if (load_cfg($data['mailer'])) {
            $this->buttons(['resend' => ['path' => 'mail-log/resend/{{ id }}', 'ranking' => 1000]]);
        }

        return $result;
    }

};
