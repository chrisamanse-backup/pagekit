<?php

use Pagekit\Config\ConfigManager;

return [

    'name' => 'config',

    'main' => function ($app) {

        $app['config'] = function ($app) {
            return new ConfigManager($app['db'], $this->config->toArray());
        };

        if ($app['config.file']) {
            $app['module']->addLoader(function ($name, array $config) use ($app) {

                if ($app['config']->has($name)) {
                    $config = array_replace_recursive($config, ['config' => $app['config']->get($name)->toArray()]);
                }

                return $config;
            });
        }

        $app->on('app.response', function () use ($app) {
            foreach ($app['config'] as $name => $config) {
                $app['config']->set($name, $config);
            }
        }, 100);

    },

    'require' => [

        'database'

    ],

    'autoload' => [

        'Pagekit\\Config\\' => 'src'

    ],

    'config' => [

        'table'  => '@system_config',
        'prefix' => 'config.',
        'cache'  => ''

    ]

];
