<?php

namespace Rithis\PageBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection,
    Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table("page_tags")
 */
class PageTag
{
    /**
     * @ORM\Column(length=64)
     * @ORM\Id
     * @var string
     */
    protected $title;

    /**
     * @ORM\ManyToMany(targetEntity="Page", mappedBy="tags")
     * @var ArrayCollection
     */
    protected $pages;

    public function __construct()
    {
        $this->pages = new ArrayCollection();
    }

    /**
     * @param string $name
     */
    public function setTitle($name)
    {
        $this->title = $name;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param Page $page
     */
    public function addPage(Page $page)
    {
        $this->pages[] = $page;
    }

    /**
     * @param Page $page
     */
    public function removePage(Page $page)
    {
        $this->pages->removeElement($page);
    }

    /**
     * @return Page
     */
    public function getPages()
    {
        return $this->pages;
    }
}
