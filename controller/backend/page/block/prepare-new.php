<?php //>

return new class('Block') extends matrix\web\backend\BlankController {

    protected function init() {
        $this->table()->module->formStyle('block-module');

        $this->buttons([
            'insert' => false,
            'next' => ['path' => 'page/{{ page_id }}/block/prepare{{ r ? "?r=#{r}" }}', 'ranking' => 200],
        ]);

        $this->columns(['module']);
    }

};
