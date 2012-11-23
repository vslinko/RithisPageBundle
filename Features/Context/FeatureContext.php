<?php

namespace Rithis\PageBundle\Features\Context;

use Behat\Symfony2Extension\Context\KernelDictionary,
    Behat\Behat\Context\BehatContext,
    Behat\Gherkin\Node\TableNode;

use PHPUnit_Framework_Assert as Assert;

use Rithis\PageBundle\Entity\PageTag,
    Rithis\PageBundle\Entity\Page;

class FeatureContext extends BehatContext
{
    use KernelDictionary;

    /**
     * @var array
     */
    private $foundPages;

    public function __construct()
    {
        $this->useContext('doctrine_schema', new DoctrineSchemaContext());
        $this->useContext('twig', new TwigContext());
    }

    /**
     * @Given /^следующие страницы:$/
     */
    public function writePagesToDatabase(TableNode $table)
    {
        $em = $this->getEntityManager();
        $tags = [];

        foreach ($table->getHash() as $pageHash) {
            $page = new Page();
            $page->setUri($pageHash['uri']);
            $page->setTitle($pageHash['title']);
            $page->setContent($pageHash['content']);

            foreach (explode(',', $pageHash['tags']) as $tagTitle) {
                if (isset($tags[$tagTitle])) {
                    $tag = $tags[$tagTitle];
                } else {
                    $tag = new PageTag();
                    $tag->setTitle($tagTitle);
                    $tags[$tagTitle] = $tag;
                }

                $page->addTag($tag);
            }

            $em->persist($page);
        }

        $em->flush();
    }

    /**
     * @When /^я выбрал из базы данных страницы с тегом "([^"]*)"$/
     */
    public function findPagesWithTag($tag)
    {
        $this->foundPages = $this->getEntityManager()->getRepository('RithisPageBundle:Page')->findByTags([$tag]);
    }

    /**
     * @Then /^количество выбранных из базы данных страниц должно быть "([^"]*)"$/
     */
    public function foundPagesCountMustEqual($expectedCount)
    {
        $count = count($this->foundPages);

        Assert::assertEquals($expectedCount, $count, "Найдено страниц: {$count}");
    }

    /**
     * @Then /^заголовок последней выбранной из базы данных страницы должен быть "([^"]*)"$/
     */
    public function lastFoundPageTitleMustEqual($expectedTitle)
    {
        $lastPage = end($this->foundPages);

        if (empty($expectedTitle) && !$lastPage) {
            return;
        }

        $title = $lastPage->getTitle();

        Assert::assertEquals($expectedTitle, $title, "Не совпадает заголовок: $title");
    }

    /**
     * @return \Doctrine\ORM\EntityManager
     */
    private function getEntityManager()
    {
        return $this->getContainer()->get('doctrine.orm.entity_manager');
    }
}
