<?php

namespace Rithis\PageBundle\Features\Context;

use Behat\CommonContexts\SymfonyDoctrineContext,
    Behat\Gherkin\Node\TableNode;

use PHPUnit_Framework_Assert as Assert;

use Gedmo\Mapping\ExtensionMetadataFactory;

class DoctrineSchemaContext extends SymfonyDoctrineContext
{
    /**
     * @var \Doctrine\ORM\Mapping\ClassMetadata
     */
    private $metadata;

    /**
     * @Given /^метаданные сущности "([^"]*)"$/
     */
    public function entityMetadata($className)
    {
        $this->metadata = $this->getEntityManager()->getClassMetadata($className);
    }

    /**
     * @Then /^в метаданных сущности должен быть числовой идентификатор с генератором последовательности$/
     */
    public function shouldBeIntegerIdentifierAndSequence()
    {
        $identifier = $this->metadata->getIdentifier();

        Assert::assertNotCount(0, $identifier, "Нет идентификатора");
        Assert::assertCount(1, $identifier, "Составной идентификатор");

        $identifierFieldMapping = $this->metadata->getFieldMapping($identifier[0]);

        Assert::assertEquals('integer', $identifierFieldMapping['type'], "Неверный тип идентификатора: {$identifierFieldMapping['type']}");

        $sequence = $this->metadata->sequenceGeneratorDefinition;

        Assert::assertNotNull($sequence, "Нет генератора последовательности");
    }

    /**
     * @Then /^в метаданных сущности должен быть указан репозиторий "([^"]*)"$/
     */
    public function customRepositoryClassNameShouldBe($expectedRepositoryClassName)
    {
        $repositoryClassName = $this->metadata->customRepositoryClassName;

        Assert::assertNotNull($repositoryClassName, "Репозиторий не задан");
        Assert::assertEquals($expectedRepositoryClassName, $repositoryClassName, "Неверный репозиторий $repositoryClassName");
    }

    /**
     * @Then /^при чтении метаданных должно быть подключено расширение GedmoSoftdeleteable$/
     */
    public function gedmoSoftdeleteableShouldBeEnabled()
    {
        $config = $this->getExtensionConfig('Gedmo\\Softdeleteable');

        Assert::assertInternalType('array', $config, "Расширение не подключено");
        Assert::assertTrue($config['softDeleteable'], "Расширение не активно");
        Assert::assertEquals('deletedAt', $config['fieldName'], "Расширение подключено для свойства {$config['fieldName']}");
    }

    /**
     * @Then /^при чтении метаданных должно быть подключено расширение GedmoTimestampable$/
     */
    public function gedmoTimestampableShouldBeEnabled()
    {
        $config = $this->getExtensionConfig('Gedmo\\Timestampable');

        Assert::assertInternalType('array', $config, "Расширение не подключено");
        Assert::assertContains('createdAt', $config['create'], "Расширение не подключено для свойства createdAt");
        Assert::assertContains('updatedAt', $config['update'], "Расширение не подключено для свойства updatedAt");
    }

    /**
     * @Then /^при чтении метаданных должно быть подключено расширение GedmoLoggable и следующие поля должны быть версионны:$/
     */
    public function gedmoLoggableShouldBeEnabledAndShouldContainVersionedFields(TableNode $fieldsTable)
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
    public function shouldBeAssociation($associationType, $className, $fieldName)
    {
        $associationMapping = $this->metadata->getAssociationMapping($fieldName);
        $targetEntityMetadata = $this->getEntityManager()->getClassMetadata($className);

        Assert::assertEquals($targetEntityMetadata->getName(), $associationMapping['targetEntity'], "Свойтво связано с {$associationMapping['targetEntity']}");
        Assert::assertEquals(constant("\\Doctrine\\ORM\\Mapping\\ClassMetadataInfo::$associationType"), $associationMapping['type'], "Неверный тип связи");
    }

    /**
     * @Then /^в метаданных сущности должны быть следующие поля:$/
     */
    public function shouldBeFields(TableNode $fieldsTable)
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
     * @param $extensionNamespace string
     * @return array
     */
    private function getExtensionConfig($extensionNamespace)
    {
        $cacheId = ExtensionMetadataFactory::getCacheId(
            $this->metadata->getName(),
            $extensionNamespace
        );

        return $this->getEntityManager()->getMetadataFactory()->getCacheDriver()->fetch($cacheId);
    }
}
