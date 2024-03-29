# RithisPageBundle [![Build Status](https://secure.travis-ci.org/rithis/RithisPageBundle.png?branch=master)](https://travis-ci.org/rithis/RithisPageBundle)

Простой бандл для работы со статическими страницами

## Установка

Запустите из консоли находясь в директории вашего проекта:

```composer require rithis/page-bundle:@dev```

Удостоверьтесь, что все необходимые бандлы подключены:

```php
$bundles = [
    new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
    new Symfony\Bundle\TwigBundle\TwigBundle(),
    new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
    new JMS\AopBundle\JMSAopBundle(),
    new JMS\DiExtraBundle\JMSDiExtraBundle($this),
    new Stof\DoctrineExtensionsBundle\StofDoctrineExtensionsBundle(),
    new Rithis\PageBundle\RithisPageBundle(),
];
```

Добавьте следующие настройки:

```yml
doctrine:
    orm:
        entity_managers:
            default:
                mappings:
                    gedmo_loggable:
                        type: annotation
                        prefix: Gedmo\Loggable\Entity
                        dir: "%kernel.root_dir%/../vendor/gedmo/doctrine-extensions/lib/Gedmo/Loggable/Entity"
                        is_bundle: false

jms_di_extra:
    locations:
        bundles: [RithisPageBundle]

stof_doctrine_extensions:
    default_locale: ru_RU
    orm:
        default:
            timestampable: true
            loggable: true
            softdeleteable: true
```

## Использование

### В шаблонах

Для вывода страницы со стандартным шаблоном:

```jinja
{% rithis_page ['some_tag'] %}
```

Для вывода страницы со своим шаблоном:

```jinja
{% rithis_page ['some_tag'] with template "my_template.html.twig" %}
```

Для получения страницы:

```jinja
{% set page = rithis_page(['some_tag']) %}
{{ page.title }}
```

## Генерация меню

Если вы используете KnpMenuBundle, то вы можете вывести меню содержащее страницы с определенными метками:

```jinja
{% set menuItem = knp_menu_get('RithisPageBundle:PageAwareBuilder:pagesMenu', [], {tags: ["header"]}) %}
{{ knp_menu_render(menuItem) }}
```

Или вы можете составить свое комбинированное меню:

```php
<?php

use Rithis\PageBundle\Menu\PageAwareBuilder,
    Knp\Menu\FactoryInterface;

class Builder extends PageAwareBuilder
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
```
