# Тестовое задание для ASG

Реализовать компонент 2.0, который выводит список элементов инфоблока, обладающего следующими свойствами: 

* Название списка
* Множественный справочник
* Множественная привязка к элементам другого инфоблока

Список должен содержать следующую информацию о каждом элементе:

* Название
* Дата изменения в формате 1 января 1970

В списке необходимо реализовать бесконечную выдачу по 3 элемента на страницу и фильтр. В фильтре отобразить название всех элементов справочника, к которым есть привязка у элементов, и названия всех элементов второго инфоблока, к которым есть привязка у элементов первого.

Фильтр работает по логике “и”.

***Входные параметры компонента***
Компонент должен получать на вход:
* Количество элементов на страницу
* Шаблон постраничной навигации
* Настройки кеширования
* Код первого инфоблока
* Код второго инфоблока

***Миграции***
Необходимо создать скрипт миграции поднимающий всю структуру базы для работы компонента (создание свойств, инфоблоков и элементов), для демонстрации работы компонента. Скрипт должен запускаться из консоли SSH.

***Общие рекомендации***
Рекомендуется использовать ORM из ядра D7 для реализации данной задачи. В случае отсутствия навыков работы с данным функционалом используйте стандартное API Битрикс.
***
# Начало работы

После установки CMS 1С-Битрикс, скопировать репозиторий.

Перейти в папку ```/local/``` запустить composer ```composer install``` , чтобы установить ```phinx``` для работы с миграциями.

Все настройки произведены, необходимо только запустить команду в командой стройке:

```php vendor/bin/phinx migrate```

Для отката миграций следует запустить команду: 

```php vendor/bin/phinx rollback -t 0```

После выполнения скриптов появится 2 инфоблока и 1 highloadblock для справочника:
1. В первом будет создано 12 элементов
2. Во втором 5 элементов
3. В highloadblock будет 3 записи

Привязка элементов в первом инфоблоке происходит случайным образом во время выполнения миграционный скриптов.

Вызов компонента происходит на главной странице.
