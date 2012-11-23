<?php

namespace Rithis\PageBundle\Menu;

use Symfony\Component\DependencyInjection\ContainerAware;

use Knp\Menu\FactoryInterface,
    Knp\Menu\ItemInterface;

class PageAwareBuilder extends ContainerAware
{
    public function pagesMenu(FactoryInterface $factory, array $options)
    {
        if (!isset($options['tags']) || !is_array($options['tags'])) {
            throw new \InvalidArgumentException("Invalid tags provided for menu generation");
        }

        $menu = $factory->createItem('root');

        $this->addPages($menu, $options['tags']);

        return $menu;
    }

    protected function addPage(ItemInterface $item, array $tags)
    {
        $page = $this->findOnePage($tags);

        $item->addChild($page->getTitle(), ['uri' => $page->getUri()]);
    }

    protected function addPages(ItemInterface $item, array $tags)
    {
        $pages = $this->findPages($tags);

        foreach ($pages as $page) {
            $item->addChild($page->getTitle(), ['uri' => $page->getUri()]);
        }
    }

    /**
     * @param array $tags
     * @return \Rithis\PageBundle\Entity\Page
     */
    protected function findOnePage(array $tags)
    {
        /** @var $repository \Rithis\PageBundle\Entity\PageRepository */
        $repository = $this->container->get('doctrine')->getManager()->getRepository('RithisPageBundle:Page');

        return $repository->findOneByTags($tags);
    }

    /**
     * @param array $tags
     * @return \Rithis\PageBundle\Entity\Page[]
     */
    protected function findPages(array $tags)
    {
        /** @var $repository \Rithis\PageBundle\Entity\PageRepository */
        $repository = $this->container->get('doctrine')->getManager()->getRepository('RithisPageBundle:Page');

        return $repository->findByTags($tags);
    }
}
