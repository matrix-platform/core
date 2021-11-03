<?php //>

return [

    'vendors' => ['icon' => 'fas fa-hands-helping', 'ranking' => 2100, 'parent' => null],

        'vendor' => ['icon' => 'fas fa-user-tie', 'ranking' => 1000, 'parent' => 'vendors', 'group' => true, 'tag' => 'query'],

            'vendor/' => ['parent' => 'vendor', 'tag' => 'query'],

            'vendor/delete' => ['parent' => 'vendor', 'tag' => 'system'],

            'vendor/insert' => ['parent' => 'vendor', 'tag' => 'insert'],

            'vendor/new' => ['parent' => 'vendor', 'tag' => 'insert'],

            'vendor/update' => ['parent' => 'vendor', 'tag' => 'update'],

            'vendor/substitute' => ['parent' => 'vendor', 'tag' => 'system'],

        'vendor-log' => ['icon' => 'far fa-list-alt', 'ranking' => 1100, 'parent' => 'vendors', 'group' => true, 'tag' => 'query'],

];
