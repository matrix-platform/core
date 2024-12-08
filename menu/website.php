<?php //>

return [

    'website' => ['icon' => 'fas fa-sitemap', 'ranking' => 3000, 'parent' => null],

        'menu' => ['icon' => 'fas fa-bars', 'ranking' => 100, 'parent' => 'website', 'pattern' => 'menu/{{ id }}/item', 'group' => true, 'tag' => 'query'],

            'menu/' => ['parent' => 'menu', 'tag' => 'query'],

            'menu/delete' => ['parent' => 'menu', 'tag' => 'delete'],

            'menu/insert' => ['parent' => 'menu', 'tag' => 'insert'],

            'menu/new' => ['parent' => 'menu', 'tag' => 'insert'],

            'menu/update' => ['parent' => 'menu', 'tag' => 'update'],

        'page' => ['icon' => 'far fa-newspaper', 'ranking' => 200, 'parent' => 'website', 'group' => true, 'tag' => 'query'],

            'page/' => ['parent' => 'page', 'tag' => 'query'],

            'page/delete' => ['parent' => 'page', 'tag' => 'delete'],

            'page/insert' => ['parent' => 'page', 'tag' => 'insert'],

            'page/new' => ['parent' => 'page', 'tag' => 'insert'],

            'page/update' => ['parent' => 'page', 'tag' => 'update'],

            'page/block' => ['parent' => 'page', 'pattern' => 'page/{{ id }}/block', 'group' => true, 'tag' => 'query'],

                'page/block/' => ['parent' => 'page/block', 'tag' => 'query'],

                'page/block/delete' => ['parent' => 'page/block', 'tag' => 'delete'],

                'page/block/insert' => ['parent' => 'page/block', 'tag' => 'insert'],

                'page/block/new' => ['parent' => 'page/block', 'tag' => 'insert'],

                'page/block/prepare' => ['parent' => 'page/block', 'tag' => 'insert'],

                'page/block/prepare-new' => ['parent' => 'page/block', 'tag' => 'insert'],

                'page/block/update' => ['parent' => 'page/block', 'tag' => 'update'],

                'page/block/item' => ['parent' => 'page/block', 'pattern' => 'page/block/{{ id }}/item', 'group' => true, 'tag' => 'query'],

                    'page/block/item/' => ['parent' => 'page/block/item', 'tag' => 'query'],

                    'page/block/item/delete' => ['parent' => 'page/block/item', 'tag' => 'delete'],

                    'page/block/item/insert' => ['parent' => 'page/block/item', 'tag' => 'insert'],

                    'page/block/item/new' => ['parent' => 'page/block/item', 'tag' => 'insert'],

                    'page/block/item/update' => ['parent' => 'page/block/item', 'tag' => 'update'],

        'privacy-policy' => ['icon' => 'far fa-file-alt', 'ranking' => 2000, 'parent' => 'website', 'group' => true, 'tag' => 'query'],

            'privacy-policy/update' => ['parent' => 'privacy-policy', 'tag' => 'update'],

        'service-terms' => ['icon' => 'fas fa-file-alt', 'ranking' => 2100, 'parent' => 'website', 'group' => true, 'tag' => 'query'],

            'service-terms/update' => ['parent' => 'service-terms', 'tag' => 'update'],

        'website-info' => ['icon' => 'fas fa-cog', 'ranking' => 2200, 'parent' => 'website', 'group' => true, 'tag' => 'query'],

            'website-info/update' => ['parent' => 'website-info', 'tag' => 'update'],

];
