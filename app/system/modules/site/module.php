<?php

return [

    'name' => 'system/site',

    'main' => 'Pagekit\\Site\\SiteModule',

    'autoload' => [

        'Pagekit\\Site\\' => 'src'

    ],

    'controllers' => [

        '@site: /site' => 'Pagekit\\Site\\Controller\\SiteController',
        '@site/api/menu: /api/site/menu' => 'Pagekit\\Site\\Controller\\MenuController',
        '@site/api/node: /api/site/node' => 'Pagekit\\Site\\Controller\\NodeController'

    ],

    'menu' => [

        'site' => [
            'label'    => 'Site',
            'icon'     => 'site:assets/images/icon-site.svg',
            'url'      => '@site',
            'active'   => '@site*',
            'priority' => 0
        ]

    ],

    'permissions' => [

        'site: manage site' => [
            'title' => 'Manage site'
        ]

    ],

    'config' => [
        'menus' => []
    ],

    'resources' => [

        'site:' => ''

    ]

];
