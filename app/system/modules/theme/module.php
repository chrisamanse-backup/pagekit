<?php

return [

    'name' => 'system/theme',

    'main' => function ($app) {

        $app->on('app.admin', function () use ($app) {

            $app['view']->map('layout', $this->path.'/templates/template.php');
            $app['view']->map('component', $this->path.'/templates/template.php');

            $app['view']->on('layout', function ($event, $view) use ($app) {

                $user = [
                    'id' => $app['user']->getId(),
                    'name' => $app['user']->getName(),
                    'email' => $app['user']->getEmail(),
                    'username' => $app['user']->getUsername()
                ];

                $view->data('$pagekit', ['user' => $user, 'menu' => $app['system']->getMenu()]);
                $event->setParameter('subset', 'latin,latin-ext');
            });

        });

    },

    'resources' => [

        'system/theme:' => ''

    ]

];
