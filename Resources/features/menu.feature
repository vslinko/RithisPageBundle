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

    Сценарий: Вывод комбинированного меню в шаблоне
        Допустим  я обновил структуру базы данных
        Также я записал в базу данных следующие страницы:
            | uri       | title             | content    | tags    |
            | /         | Главная           | Содержание | header  |
            | /contacts | Контакты          | Содержание | header  |
            | /profile  | Профиль           | Содержание | actions |
            | /admin    | Администрирование | Содержание | actions |
        Также я сохранил шаблон "menu.html.twig" со следующим содержанием:
            """
            {% spaceless %}{{ knp_menu_render('composite') }}{% endspaceless %}
            """
        И я вывел шаблон "menu.html.twig"
        Тогда вывод шаблона должен содержать:
            """
            <ul><li class="first"><a href="/">Главная</a></li><li><a href="/first">First</a></li><li><a href="/profile">Профиль</a></li><li class="last"><a href="/admin">Администрирование</a></li></ul>
            """
