<?php

namespace Pagekit\Blog;

use Pagekit\Application as App;
use Pagekit\Blog\Content\ReadmorePlugin;
use Pagekit\Blog\Event\CommentListener;
use Pagekit\Blog\Event\RouteListener;
use Pagekit\Site\Model\MountType;
use Pagekit\Site\Model\UrlType;
use Pagekit\System\Extension;

class BlogExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function main(App $app)
    {
        $app->subscribe(
            new RouteListener,
            new CommentListener,
            new ReadmorePlugin
        );

        $app->on('app.request', function() use ($app) {
            $app['module']->get('system/site')->setFrontpage('@blog/site');
        }, 175);

        $app->on('app.request', function() use ($app) {
            $app['scripts']->register('blog-site', 'blog:app/bundle/site.js', '~site');
        });

        $app->on('site.types', function ($event, $site) {
            $site->registerType(new UrlType('blog-post', 'Blog Post', '@blog/id'));
            $site->registerType(new MountType('blog', 'Blog', 'Pagekit\\Blog\\Controller\\SiteController', '@blog/site'));
        });

    }

    public function enable()
    {
        if ($version = App::migrator()->create('extensions/blog/migrations', $this->config('version'))->run()) {
            App::config($this->name)->set('version', $version);
        }
    }

    public function uninstall()
    {
        App::config()->remove($this->name);
    }

    public function getPermalink()
    {
        $permalink = $this->config('permalink.type');

        if ($permalink == 'custom') {
            $permalink = $this->config('permalink.custom');
        }

        return $permalink;
    }
}
