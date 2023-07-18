<?php //>

return new class('Block') extends matrix\web\backend\ListController {

    protected function init() {
        $table = $this->table();

        $table->add('item_count', 'item.count');

        $this->columns('module', 'title', 'item_count');

        $this->controls([
            'new' => $this->permitted('page/block/prepare-new') ? ['path' => 'page/{{ page_id }}/block/prepare-new', 'ranking' => 100] : false,
        ]);
    }

    protected function postprocess($form, $result) {
        foreach ($result['data'] as &$data) {
            $module = load_cfg("module/{$data['module']}");

            if (!$module['sub-module']) {
                unset($data['item_count']);
            }
        }

        return $result;
    }

};
