<?php //>

return new class('File') extends matrix\web\backend\GetController {

    protected function init() {
        $table = $this->table();

        $table->size->group(1);
        $table->modified_time->group(1);

        $table->width->group(2);
        $table->height->group(2);
        $table->seconds->group(2);

        $names = [
            'name',
            'description',
            'size',
            'width',
            'height',
            'seconds',
            'modified_time',
        ];

        $this->columns($table->getColumns($names));
    }

    protected function postprocess($form, $result) {
        $data = $result['data'];
        $table = $this->table();

        if ($data['width'] === null || $data['height'] === null) {
            $table->width->invisible(true);
            $table->height->invisible(true);
        }

        if ($data['seconds'] === null) {
            $table->seconds->invisible(true);
        }

        return $result;
    }

};
