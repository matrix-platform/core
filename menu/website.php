<?php //>

return [

    'website' => ['icon' => 'fas fa-sitemap', 'ranking' => 3000, 'parent' => null],

        'menu' => ['icon' => 'fas fa-bars', 'ranking' => 100, 'parent' => 'website', 'pattern' => 'menu/{{ id }}/item', 'group' => true, 'tag' => 'query'],

            'menu/' => ['parent' => 'menu', 'tag' => 'query'],

            'menu/delete' => ['parent' => 'menu', 'tag' => 'delete'],

            'menu/insert' => ['parent' => 'menu', 'tag' => 'insert'],

            'menu/new' => ['parent' => 'menu', 'tag' => 'insert'],

            'menu/update' => ['parent' => 'menu', 'tag' => 'update'],

];
