<?php //>

return [

    'system' => ['icon' => 'fas fa-desktop', 'ranking' => 9000, 'parent' => null],

        'authority' => ['icon' => 'fas fa-users-cog', 'ranking' => 100, 'parent' => 'system'],

            'user' => ['icon' => 'fas fa-user', 'ranking' => 100, 'parent' => 'authority', 'group' => true, 'tag' => 'query'],

                'user/' => ['parent' => 'user', 'tag' => 'query'],

                'user/delete' => ['parent' => 'user', 'tag' => 'delete'],

                'user/insert' => ['parent' => 'user', 'tag' => 'insert'],

                'user/new' => ['parent' => 'user', 'tag' => 'insert'],

                'user/update' => ['parent' => 'user', 'tag' => 'update'],

            'user-log' => ['icon' => 'far fa-list-alt', 'ranking' => 200, 'parent' => 'authority', 'group' => true, 'tag' => 'query'],

                'user-log/' => ['parent' => 'user-log', 'tag' => 'query'],

            'group' => ['icon' => 'fas fa-users', 'ranking' => 300, 'parent' => 'authority', 'group' => true, 'tag' => 'query'],

                'group/' => ['parent' => 'group', 'tag' => 'query'],

                'group/delete' => ['parent' => 'group', 'tag' => 'delete'],

                'group/insert' => ['parent' => 'group', 'tag' => 'insert'],

                'group/new' => ['parent' => 'group', 'tag' => 'insert'],

                'group/update' => ['parent' => 'group', 'tag' => 'update'],

        'cfg' => ['icon' => 'fas fa-cogs', 'ranking' => 200, 'parent' => 'system'],

            'cfg/base' => ['icon' => 'fas fa-cog', 'ranking' => 100, 'parent' => 'cfg', 'group' => true, 'tag' => 'query'],

                'cfg/base/' => ['parent' => 'cfg/base', 'tag' => 'query'],

                'cfg/base/update' => ['parent' => 'cfg/base', 'tag' => 'update'],

        'i18n' => ['icon' => 'fas fa-globe', 'ranking' => 300, 'parent' => 'system'],

            'i18n/base' => ['icon' => 'fas fa-language', 'ranking' => 100, 'parent' => 'i18n', 'group' => true, 'tag' => 'query'],

                'i18n/base/' => ['parent' => 'i18n/base', 'tag' => 'query'],

                'i18n/base/update' => ['parent' => 'i18n/base', 'tag' => 'update'],

            'i18n/menu' => ['icon' => 'fas fa-bars', 'ranking' => 200, 'parent' => 'i18n', 'group' => true, 'tag' => 'system'],

                'i18n/menu/' => ['parent' => 'i18n/menu', 'tag' => 'system'],

                'i18n/menu/update' => ['parent' => 'i18n/menu', 'tag' => 'system'],

            'i18n/options' => ['icon' => 'fas fa-check', 'ranking' => 300, 'parent' => 'i18n', 'group' => true, 'tag' => 'system'],

                'i18n/options/' => ['parent' => 'i18n/options', 'tag' => 'system'],

                'i18n/options/update' => ['parent' => 'i18n/options', 'tag' => 'system'],

            'i18n/table' => ['icon' => 'fas fa-table', 'ranking' => 400, 'parent' => 'i18n', 'group' => true, 'tag' => 'system'],

                'i18n/table/' => ['parent' => 'i18n/table', 'tag' => 'system'],

                'i18n/table/update' => ['parent' => 'i18n/table', 'tag' => 'system'],

            'i18n/template' => ['icon' => 'far fa-comment-dots', 'ranking' => 500, 'parent' => 'i18n', 'group' => true, 'tag' => 'query'],

                'i18n/template/' => ['parent' => 'i18n/template', 'tag' => 'query'],

                'i18n/template/update' => ['parent' => 'i18n/template', 'tag' => 'update'],

        'sms-log' => ['icon' => 'fas fa-sms', 'ranking' => 400, 'parent' => 'system', 'group' => true, 'tag' => 'query'],

            'sms-log/' => ['parent' => 'sms-log', 'tag' => 'query'],

        'mail-log' => ['icon' => 'fas fa-at', 'ranking' => 500, 'parent' => 'system', 'group' => true, 'tag' => 'query'],

            'mail-log/' => ['parent' => 'mail-log', 'tag' => 'query'],

        'system-log' => ['icon' => 'far fa-list-alt', 'ranking' => 600, 'parent' => 'system', 'group' => true, 'tag' => 'query'],

            'system-log/' => ['parent' => 'system-log', 'tag' => 'query'],

        'other' => ['ranking' => 900, 'parent' => 'system'],

            'deployment' => ['parent' => 'other', 'group' => true, 'tag' => 'user'],

                'deployment/' => ['parent' => 'deployment', 'tag' => 'user'],

                'deployment/update' => ['parent' => 'deployment', 'tag' => 'user'],

            'file-info' => ['parent' => 'other', 'group' => true, 'tag' => 'user'],

                'file-info/' => ['parent' => 'file-info', 'tag' => 'user'],

                'file-info/update' => ['parent' => 'file-info', 'tag' => 'user'],

            'label' => ['parent' => 'other', 'group' => true, 'tag' => 'update'],

                'label/update' => ['parent' => 'label', 'tag' => 'update'],

];
