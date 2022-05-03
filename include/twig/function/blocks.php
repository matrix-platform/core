<?php //>

return new Twig\TwigFunction('blocks', function ($page) {
    $blocks = [];
    $modules = [];

    foreach (model('Block')->query(['page_id' => $page['id']]) as $block) {
        $module = load_cfg("module/{$block['module']}");

        if ($module['sub-module']) {
            $modules[$block['id']] = load_cfg("sub-module/{$module['sub-module']}");
        }

        $extra = json_decode($block['extra'], true);

        if ($extra) {
            foreach ($module['fields'] as $name => $field) {
                if ($field->multilingual()) {
                    $local = $name . '__' . LANGUAGE;

                    if (key_exists($local, $extra)) {
                        $extra[$name] = $extra[$local];
                    }
                }
            }

            $block = array_merge($block, $extra);
        }

        if (is_null($block['fluid'])) {
            $block['fluid'] = @$page['fluid'];
        }

        $blocks[$block['id']] = $block;
    }

    foreach (model('BlockItem')->query(['block_id' => array_keys($blocks)]) as $item) {
        $extra = json_decode($item['extra'], true);

        if ($extra) {
            foreach ($modules[$item['block_id']]['fields'] as $name => $field) {
                if ($field->multilingual()) {
                    $local = $name . '__' . LANGUAGE;

                    if (key_exists($local, $extra)) {
                        $extra[$name] = $extra[$local];
                    }
                }
            }

            $item = array_merge($item, $extra);
        }

        $blocks[$item['block_id']]['items'][] = $item;
    }

    return $blocks;
});
