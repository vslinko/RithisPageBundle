<?php

namespace Rithis\PageBundle\Features\Context;

use Behat\Symfony2Extension\Context\KernelDictionary,
    PHPUnit_Framework_Assert as Assert,
    Behat\Behat\Context\BehatContext,
    Behat\Gherkin\Node\PyStringNode,
    Behat\Gherkin\Node\TableNode;

use Gedmo\Mapping\ExtensionMetadataFactory,
    Doctrine\ORM\Tools\SchemaTool;

use Rithis\PageBundle\Entity\PageTag,
    Rithis\PageBundle\Entity\Page;

use Twig_Loader_Array,
    Twig_Loader_Chain,
    Twig_Environment;

class FeatureContext extends BehatContext
{
    use KernelDictionary;

    /**
     * @var \Doctrine\ORM\Mapping\ClassMetadata
     */
    private $metadata;

    /**
     * @var array
     */
    private $foundPages;

    /**
     * @var Page
     */
    private $page;

    /**
     * @var \Twig_Loader_Array
     */
    private $twigLoader;

    /**
     * @var string
     */
    private $templateOutput;

    /**
     * @Given /^я прочел метаданные сущности "([^"]*)"$/
     */
    public function readEntityMetadata($className)
    {
        $this->metadata = $this->getEntityManager()->getClassMetadata($className);
    }

    /**
     * @Then /^в метаданных сущности должен быть идентификатор с генератором последовательности$/
     */
    public function entityMetadataMustContainIdentifierAndSequence()
    {
        $identifier = $this->metadata->getIdentifier();

        Assert::assertNotCount(0, $identifier, "Нет идентификатора");
        Assert::assertCount(1, $identifier, "Составной идентификатор");

        $identifierFieldMapping = $this->metadata->getFieldMapping($identifier[0]);
        $sequence = $this->metadata->sequenceGeneratorDefinition;

        Assert::assertEquals('integer', $identifierFieldMapping['type'], "Неверный тип идентификатора: {$identifierFieldMapping['type']}");
        Assert::assertNotNull($sequence, "Нет генератора последовательности");
    }

    /**
     * @Then /^в метаданных сущности должен быть репозиторий "([^"]*)"$/
     */
    public function entityMetadataCustomRepositoryClassMustBe($expectedRepositoryClassName)
    {
        Assert::assertNotNull($this->metadata->customRepositoryClassName, "Репозиторий не задан");

        $repositoryClassName = $this->metadata->customRepositoryClassName;
        Assert::assertEquals($expectedRepositoryClassName, $repositoryClassName, "Неверный репозиторий $repositoryClassName");
    }

    /**
     * @Then /^при чтении метаданных должно быть подключено расширение GedmoSoftdeleteable$/
     */
    public function whenEntityMetadataWasReadGedmoSoftdeleteableMustBeEnabled()
    {
        $config = $this->getExtensionConfig('Gedmo\\Softdeleteable');

        Assert::assertInternalType('array', $config, "Расширение не подключено");
        Assert::assertTrue($config['softDeleteable'], "Расширение не активно");
        Assert::assertEquals('deletedAt', $config['fieldName'], "Расширение подключено для свойства {$config['fieldName']}");
    }

    /**
     * @Then /^при чтении метаданных должно быть подключено расширение GedmoTimestampable$/
     */
    public function whenEntityMetadataWasReadGedmoTimestampableMustBeEnabled()
    {
        $config = $this->getExtensionConfig('Gedmo\\Timestampable');

        Assert::assertInternalType('array', $config, "Расширение не подключено");
        Assert::assertContains('createdAt', $config['create'], "Расширение не подключено для свойства createdAt");
        Assert::assertContains('updatedAt', $config['update'], "Расширение не подключено для свойства updatedAt");
    }

    /**
     * @Then /^при чтении метаданных должно быть подключено расширение GedmoLoggable и следующие поля должны быть версионны:$/
     */
    public function whenEntityMetadataWasReadGedmoLoggableMustBeEnabledAndThisFieldsMustBeVersioned(TableNode $fieldsTable)
    {
        $config = $this->getExtensionConfig('Gedmo\\Loggable');

        Assert::assertInternalType('array', $config, "Расширение не подключено");
        Assert::assertTrue($config['loggable'], "Расширение не активно");

        $expectedFields = array_map(function ($row) { return $row[0]; }, $fieldsTable->getRows());

        Assert::assertEquals($expectedFields, $config['versioned'], "Версионные поля не совпадают");
    }

    /**
     * @Then /^в метаданных сущности должна быть связь "([^"]*)" с сущностью "([^"]*)" у свойства "([^"]*)"$/
     */
    public function entityMetadataMustContainAssociation($associationType, $className, $fieldName)
    {
        $associationMapping = $this->metadata->getAssociationMapping($fieldName);
        $targetEntityMetadata = $this->getEntityManager()->getClassMetadata($className);

        Assert::assertEquals($targetEntityMetadata->getName(), $associationMapping['targetEntity'], "Свойтво связано с {$associationMapping['targetEntity']}");
        Assert::assertEquals(constant("\\Doctrine\\ORM\\Mapping\\ClassMetadataInfo::$associationType"), $associationMapping['type'], "Неверный тип связи");
    }

    /**
     * @Then /^в метаданных сущности должны быть следующие поля:$/
     */
    public function entityMetadataMustContainThisFields(TableNode $fieldsTable)
    {
        $rowsHash = $fieldsTable->getRowsHash();

        $expectedFieldNames = array_keys($rowsHash);
        $fieldNames = $this->metadata->getFieldNames();
        sort($expectedFieldNames);
        sort($fieldNames);
        Assert::assertEquals($expectedFieldNames, $fieldNames, "Список полей не совпадает");

        foreach ($rowsHash as $name => $expectedType) {
            $type = $this->metadata->getFieldMapping($name)['type'];
            Assert::assertEquals($expectedType, $type, "Тип поля $name: $type");
        }
    }

    /**
     * @Given /^я обновил структуру базы данных/
     */
    public function updateDatabaseSchema()
    {
        $em = $this->getEntityManager();

        $tool = new SchemaTool($em);
        $tool->createSchema($em->getMetadataFactory()->getAllMetadata());
    }

    /**
     * @Given /^я записал в базу данных следующие страницы:$/
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
     * @Given /^я выбрал из базы данных страницы с тегом "([^"]*)"$/
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
        $title = end($this->foundPages)->getTitle();

        Assert::assertEquals($expectedTitle, $title, "Не совпадает заголовок: $title");
    }

    /**
     * @Given /^я выбрал в шаблоне страницу с тегом "([^"]*)"$/
     */
    public function findPageWithTagFromTemplate($tag)
    {
        /** @var $twig \Twig_Environment */
        $twig = $this->getContainer()->get('twig');

        Assert::assertTrue($twig->hasExtension('rithis-page'), "Расширение не подключенно");

        /** @var $extension \Rithis\PageBundle\Twig\PageExtension */
        $extension = $twig->getExtension('rithis-page');
        Assert::assertInstanceOf('Rithis\\PageBundle\\Twig\\PageExtension', $extension);

        $this->page = $extension->getPage([$tag]);
    }

    /**
     * @Then /^заголовок выбранной в шаблоне страницы должен быть "([^"]*)"$/
     */
    public function foundedFromTemplatePageTitleMustEqual($title)
    {
        Assert::assertEquals($title, $this->page->getTitle());
    }

    /**
     * @Given /^я сохранил шаблон "([^"]*)" со следующим содержанием:$/
     */
    public function saveTemplate($name, PyStringNode $content)
    {
        if (!$this->twigLoader) {
            $this->twigLoader = new Twig_Loader_Array([]);

            /** @var $twig \Twig_Environment */
            $twig = $this->getContainer()->get('twig');

            $twig->setLoader(new Twig_Loader_Chain([
                $this->twigLoader,
                $twig->getLoader()
            ]));
        }

        $this->twigLoader->setTemplate($name, $content->getRaw());
    }

    /**
     * @Given /^я вывел шаблон "([^"]*)"$/
     */
    public function renderTemplate($name)
    {
        $this->templateOutput = $this->getContainer()->get('twig')->render($name);
    }

    /**
     * @Then /^вывод шаблона должен содержать "([^"]*)"$/
     */
    public function templateOutputMustContain($string)
    {
        Assert::assertContains($string, $this->templateOutput, "Вхождения нет");
    }

    /**
     * @param $extensionNamespace string
     * @return array
     */
    private function getExtensionConfig($extensionNamespace)
    {
        $metadataFactory = $this->getEntityManager()->getMetadataFactory();

        $cacheId = ExtensionMetadataFactory::getCacheId(
            $this->metadata->getName(),
            $extensionNamespace
        );

        return $metadataFactory->getCacheDriver()->fetch($cacheId);
    }

    /**
     * @return \Doctrine\ORM\EntityManager
     */
    private function getEntityManager()
    {
        return $this->getContainer()->get('doctrine.orm.entity_manager');
    }
}
