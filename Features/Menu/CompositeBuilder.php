<?php

namespace Rithis\PageBundle\Features\Menu;

use Rithis\PageBundle\Menu\PageAwareBuilder,
    Knp\Menu\FactoryInterface;

class CompositeBuilder extends PageAwareBuilder
{
    public function mainMenu(FactoryInterface $factory, array $options)
    {
        $menu = $factory->createItem('root');

        $this->addPage($menu, ['header']);
        $menu->addChild('First', ['uri' => '/first']);
        $this->addPages($menu, ['actions']);

        return $menu;
    }
}
