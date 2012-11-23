# language: ru
Функционал: Расширение шаблонизатора

    Сценарий: Получение страницы в шаблоне
        Дано шаблон "page.html.twig" со следующим содержанием:
            """
            {% spaceless %}
            {% set page = rithis_page(["header"]) %}
            <li>{{ page.title }}</li>
            {% set page = rithis_page(["footer"]) %}
            <li>{{ page.title }}</li>
            {% endspaceless %}
            """
        И следующие страницы:
            | uri       | title      | content    | tags          |
            | /         | Главная    | Главная    | header        |
            | /about    | О компании | О компании | header,footer |
            | /contacts | Контакты   | Контакты   | footer        |
        Если я вывел шаблон "page.html.twig"
        Тогда вывод шаблона должен содержать:
            """
            <li>Главная</li><li>О компании</li>
            """

    Сценарий: Вывод страницы
        Дано шаблон "header.html.twig" со следующим содержанием:
            """
            {% rithis_page ["header"] %}
            """
        И следующие страницы:
            | uri | title     | content    | tags   |
            | /   | Заголовок | Содержание | header |
        Если я вывел шаблон "header.html.twig"
        Тогда вывод шаблона должен содержать:
            """
            Заголовок
            """
        Также вывод шаблона должен содержать:
            """
            Содержание
            """

    Сценарий: Вывод страницы используя свой шаблон
        Дано шаблон "page.html.twig" со следующим содержанием:
            """
            <a>{{ page.title }}</a>
            <b>{{ page.content }}</b>
            """
        И шаблон "header.html.twig" со следующим содержанием:
            """
            {% spaceless %}
            {% rithis_page ["header"] with template "page.html.twig" %}
            {% endspaceless %}
            """
        Также следующие страницы:
            | uri | title     | content    | tags   |
            | /   | Заголовок | Содержание | header |
        Если я вывел шаблон "header.html.twig"
        То вывод шаблона должен содержать:
            """
            <a>Заголовок</a><b>Содержание</b>
            """
