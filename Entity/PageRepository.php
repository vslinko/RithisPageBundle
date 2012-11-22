<?php

namespace Rithis\PageBundle\Entity;

use Doctrine\ORM\EntityRepository;

class PageRepository extends EntityRepository
{
    public function findByTags(array $tags)
    {
        return $this->createQueryBuilder('p')
            ->join('p.tags', 't')
            ->where('t.title = :tags')
            ->setParameter('tags', $tags)
            ->getQuery()
            ->getResult();
    }

    public function findOneByTags(array $tags)
    {
        $pages = $this->findByTags($tags);

        return count($pages) > 0 ? $pages[0] : null;
    }
}
