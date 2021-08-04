<?php //>

return new Twig\TwigFunction('blocks', function ($page_id) {
    $blocks = [];
    $modules = [];

    foreach (model('Block')->query(['page_id' => $page_id]) as $block) {
        $module = load_cfg("module/{$block['module']}");

        if ($module['sub-module']) {
            $modules[$block['id']] = load_cfg("sub-module/{$module['sub-module']}");
        }

        $extra = json_decode($block['extra'], true);

        if ($extra) {
            foreach ($module['fields'] as $name => $field) {
                if ($field->multilingual()) {
                    $extra[$name] = @$extra[$name . '__' . LANGUAGE];
                }
            }

            $block = array_merge($block, $extra);
        }

        $blocks[$block['id']] = $block;
    }

    foreach (model('BlockItem')->query(['block_id' => array_keys($blocks)]) as $item) {
        $extra = json_decode($item['extra'], true);

        if ($extra) {
            foreach ($modules[$item['block_id']]['fields'] as $name => $field) {
                if ($field->multilingual()) {
                    $extra[$name] = @$extra[$name . '__' . LANGUAGE];
                }
            }

            $item = array_merge($item, $extra);
        }

        $blocks[$item['block_id']]['items'][] = $item;
    }

    return $blocks;
});
