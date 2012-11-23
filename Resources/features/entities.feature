# language: ru
Функционал: Сущности

    Сценарий: Метаданные сущности Rithis\PageBundle\Entity\Page
        Дано метаданные сущности "Rithis\PageBundle\Entity\Page"
        Тогда в метаданных сущности должен быть числовой идентификатор с генератором последовательности
        Также в метаданных сущности должен быть указан репозиторий "Rithis\PageBundle\Entity\PageRepository"
        К тому же при чтении метаданных должно быть подключено расширение GedmoSoftdeleteable
        Также при чтении метаданных должно быть подключено расширение GedmoTimestampable
        Также при чтении метаданных должно быть подключено расширение GedmoLoggable и следующие поля должны быть версионны:
            | uri     |
            | title   |
            | content |
        Также в метаданных сущности должна быть связь "MANY_TO_MANY" с сущностью "Rithis\PageBundle\Entity\PageTag" у свойства "tags"
        И в метаданных сущности должны быть следующие поля:
            | id        | integer  |
            | uri       | string   |
            | title     | string   |
            | content   | text     |
            | createdAt | datetime |
            | updatedAt | datetime |
            | deletedAt | datetime |

    Сценарий: Метаданные сущности Rithis\PageBundle\Entity\PageTag
        Дано метаданные сущности "Rithis\PageBundle\Entity\PageTag"
        Тогда в метаданных сущности должна быть связь "MANY_TO_MANY" с сущностью "Rithis\PageBundle\Entity\Page" у свойства "pages"
        И в метаданных сущности должны быть следующие поля:
            | title | string  |

    Структура сценария: Выборка страниц по тегам
        Дано следующие страницы:
            | uri       | title      | content    | tags          |
            | /         | Главная    | Главная    | header,footer |
            | /about    | О компании | О компании | header,footer |
            | /contacts | Контакты   | Контакты   | footer        |
        Допустим я выбрал из базы данных страницы с тегом "<tag>"
        Тогда количество выбранных из базы данных страниц должно быть "<count>"
        И заголовок последней выбранной из базы данных страницы должен быть "<title>"

        Примеры:
            | tag       | count | title      |
            | header    | 2     | О компании |
            | footer    | 3     | Контакты   |
            | undefined | 0     |            |
