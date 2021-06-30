<?php //>

return [

    'common' => ['parent' => null, 'group' => true],

        'file-info/' => ['parent' => 'common', 'tag' => 'user'],

        'file-info/update' => ['parent' => 'common', 'tag' => 'user'],

    'system' => ['icon' => 'fas fa-desktop', 'ranking' => 9000, 'parent' => null],

        'other' => ['ranking' => 900, 'parent' => 'system'],

            'label' => ['parent' => 'other', 'group' => true, 'tag' => 'update'],

                'label/update' => ['parent' => 'label', 'tag' => 'update'],

];
