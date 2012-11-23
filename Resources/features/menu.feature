# language: ru
Функционал: Вывод меню

    Сценарий: Вывод меню в шаблоне содержащее страницы с определенными метками
        Дано шаблон "menu.html.twig" со следующим содержанием:
            """
            {% set menuItem = knp_menu_get('RithisPageBundle:PageAwareBuilder:pagesMenu', [], {tags: ["header"]}) %}
            {{ knp_menu_render(menuItem) }}
            """
        И следующие страницы:
            | uri | title     | content    | tags   |
            | /   | Заголовок | Содержание | header |
        Если я вывел шаблон "menu.html.twig"
        То вывод шаблона должен содержать:
            """
            <a href="/">Заголовок</a>
            """

    Сценарий: Проверка комбинированного меню Rithis\PageBundle\Features\Menu\CompositeBuilder
        Дано шаблон "menu.html.twig" со следующим содержанием:
            """
            {% spaceless %}{{ knp_menu_render('composite') }}{% endspaceless %}
            """
        И следующие страницы:
            | uri       | title             | content    | tags    |
            | /         | Главная           | Содержание | header  |
            | /contacts | Контакты          | Содержание | header  |
            | /profile  | Профиль           | Содержание | actions |
            | /admin    | Администрирование | Содержание | actions |
        Если я вывел шаблон "menu.html.twig"
        Тогда вывод шаблона должен содержать:
            """
            <ul><li class="first"><a href="/">Главная</a></li><li><a href="/first">First</a></li><li><a href="/profile">Профиль</a></li><li class="last"><a href="/admin">Администрирование</a></li></ul>
            """
