<?php

namespace Rithis\PageBundle\Entity;

use Gedmo\Timestampable\Traits\TimestampableEntity,
    Doctrine\Common\Collections\ArrayCollection,
    Gedmo\Mapping\Annotation as Gedmo,
    Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Rithis\PageBundle\Entity\PageRepository")
 * @ORM\Table("pages")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt")
 * @Gedmo\Loggable
 */
class Page
{
    use TimestampableEntity;

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\SequenceGenerator
     * @var int
     */
    protected $id;

    /**
     * @ORM\Column(nullable=true)
     * @Gedmo\Versioned
     * @var string
     */
    protected $uri;

    /**
     * @ORM\Column(length=140)
     * @Gedmo\Versioned
     * @var string
     */
    protected $title;

    /**
     * @ORM\Column(type="text")
     * @Gedmo\Versioned
     * @var string
     */
    protected $content;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @var \DateTime
     */
    protected $deletedAt;

    /**
     * @ORM\ManyToMany(targetEntity="PageTag", inversedBy="pages", cascade={"persist"})
     * @ORM\JoinTable("pages_tags",
     *      joinColumns={@ORM\JoinColumn(name="pageId", referencedColumnName="id", nullable=false)},
     *      inverseJoinColumns={@ORM\JoinColumn(name="tagTitle", referencedColumnName="title", nullable=false)}
     * )
     * @var ArrayCollection
     */
    protected $tags;

    public function __construct()
    {
        $this->tags = new ArrayCollection();
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $uri
     */
    public function setUri($uri)
    {
        $this->uri = $uri;
    }

    /**
     * @return string
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param \DateTime|null $deletedAt
     */
    public function setDeletedAt($deletedAt)
    {
        $this->deletedAt = $deletedAt;
    }

    /**
     * @return \DateTime
     */
    public function getDeletedAt()
    {
        return $this->deletedAt;
    }

    /**
     * @param PageTag $tag
     */
    public function addTag(PageTag $tag)
    {
        $this->tags[] = $tag;
    }

    /**
     * @param PageTag $tag
     */
    public function removeTag(PageTag $tag)
    {
        $this->tags->removeElement($tag);
    }

    /**
     * @return PageTag
     */
    public function getTags()
    {
        return $this->tags;
    }
}
