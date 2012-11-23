<?php

namespace Rithis\PageBundle\Twig;

use Twig_Function_Method,
    Twig_Extension;

use JMS\DiExtraBundle\Annotation as DI;

use Doctrine\ORM\EntityManager;

/**
 * @DI\Service(public=false)
 * @DI\Tag("twig.extension")
 */
class PageExtension extends Twig_Extension
{
    private $em;

    /**
     * @DI\InjectParams({
     *      "em"=@DI\Inject("doctrine.orm.entity_manager")
     * })
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function getFunctions()
    {
        return [
            'rithis_page' => new Twig_Function_Method($this, 'getPage'),
        ];
    }

    public function getTokenParsers()
    {
        return [
            new RithisPageTokenParser(),
        ];
    }

    public function getPage(array $tags)
    {
        return $this->em->getRepository('RithisPageBundle:Page')->findOneByTags($tags);
    }

    public function getDefaultTemplate()
    {
        return 'RithisPageBundle:Page:get.html.twig';
    }

    public function getName()
    {
        return 'rithis-page';
    }
}
