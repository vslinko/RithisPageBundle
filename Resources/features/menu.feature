# language: ru
Функционал: Вывод меню

    Сценарий: Вывод меню в шаблоне содержащее страницы с определенными метками
        Допустим  я обновил структуру базы данных
        Также я записал в базу данных следующие страницы:
            | uri | title     | content    | tags   |
            | /   | Заголовок | Содержание | header |
        Также я сохранил шаблон "menu.html.twig" со следующим содержанием:
            """
            {% set menuItem = knp_menu_get('RithisPageBundle:PageAwareBuilder:pagesMenu', [], {tags: ["header"]}) %}
            {{ knp_menu_render(menuItem) }}
            """
        И я вывел шаблон "menu.html.twig"
        Тогда вывод шаблона должен содержать:
            """
            <a href="/">Заголовок</a>
            """
