<?php //>

return [

    'members' => ['icon' => 'fas fa-user-friends', 'ranking' => 2000, 'parent' => null],

        'member' => ['icon' => 'far fa-user', 'ranking' => 1000, 'parent' => 'members', 'group' => true, 'tag' => 'query'],

            'member/' => ['parent' => 'member', 'tag' => 'query'],

            'member/delete' => ['parent' => 'member', 'tag' => 'system'],

            'member/insert' => ['parent' => 'member', 'tag' => 'system'],

            'member/new' => ['parent' => 'member', 'tag' => 'system'],

            'member/update' => ['parent' => 'member', 'tag' => 'update'],

            'member/substitute' => ['parent' => 'member', 'tag' => 'system'],

        'member-log' => ['icon' => 'far fa-list-alt', 'ranking' => 1100, 'parent' => 'members', 'group' => true, 'tag' => 'query'],

];
