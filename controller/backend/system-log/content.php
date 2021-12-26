<?php //>

return new class('SystemLog') extends matrix\web\backend\GetController {

    protected function postprocess($form, $result) {
        switch ($result['data']['type']) {
        case 1:
        case 2:
            $table = $this->table();
            $table->content->invisible(true);
            break;
        }

        return $result;
    }

};
