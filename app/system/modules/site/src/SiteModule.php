<?php

namespace Pagekit\Site;

use Pagekit\Application as App;
use Pagekit\Module\Module;
use Pagekit\Site\Entity\Node;
use Pagekit\Site\Model\TypeInterface;
use Pagekit\Site\Model\UrlType;

class SiteModule extends Module
{
    protected $types;
    protected $menus;
    protected $frontpage;

    /**
     * {@inheritdoc}
     */
    public function main(App $app)
    {
        $app['node'] = function($app) {
            if ($id = $app['request']->attributes->get('_node') and $node = Node::find($id)) {
                return $node;
            }

            return new Node;
        };

        $app->on('app.request', function() use ($app) {
            foreach (Node::where(['status = ?'], [1])->get() as $node) {
                if ($type = $this->getType($node->getType())) {

                    if ($node->get('frontpage')) {
                        $this->setFrontpage($type->getLink($node));
                    }

                    $type->bind($node);
                }
            }

        }, 150);

        $app->on('app.request', function() use ($app) {
            if ($this->frontpage) {
                $app['aliases']->add('/', $this->frontpage);
            } else {
                $app['callbacks']->get('/', '_frontpage', function() {
                    return __('No Frontpage assigned.');
                });
            }
        }, 125);

    }

    /**
     * @param  string $type
     * @return TypeInterface
     */
    public function getType($type)
    {
        $types = $this->getTypes();

        return isset($types[$type]) ? $types[$type] : null;
    }

    /**
     * @return array
     */
    public function getTypes()
    {
        if (!$this->types) {

            $this->registerType(new UrlType('alias', __('Alias')));

            App::trigger('site.types', [$this]);
        }

        return $this->types;
    }

    /**
     * Registers a node type.
     *
     * @param TypeInterface $type
     */
    public function registerType(TypeInterface $type)
    {
        $this->types[$type->getId()] = $type;
    }

    /**
     * @return array
     */
    public function getMenus()
    {
        if (!$this->menus) {

            foreach (App::module() as $module) {

                if (!isset($module->menus)) {
                    continue;
                }

                foreach ($module->menus as $id => $menu) {
                    $this->registerMenu($id, $menu, ['fixed' => true]);
                }
            }

            foreach ($this->config('menus') as $menu) {
                $this->registerMenu($menu['id'], $menu['label']);
            }

            App::trigger('site.menus', [$this]);
        }

        return $this->menus;
    }

    /**
     * Registers a menu.
     *
     * @param string $id
     * @param string $label
     * @param array  $options
     */
    public function registerMenu($id, $label, array $options = [])
    {
        $this->menus[$id] = array_merge($options, compact('id', 'label'));
    }

    /**
     * Gets the sites frontpage route.
     *
     * @return string
     */
    public function getFrontpage()
    {
        return $this->frontpage;
    }

    /**
     * Sets the sites frontpage route.
     *
     * @param string $name
     */
    public function setFrontpage($name)
    {
        $this->frontpage = $name;
    }
}
