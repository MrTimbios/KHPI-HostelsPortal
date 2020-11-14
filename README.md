# KHPI-HostelsPortal

Моя бакалаврская дипломная работа.

[Демонстративная версия сайта](https://espo.co.ua/)

## Установка

Чтобы запустить скрипт, вам нужно отредактировать ```config.php``` и ```dbconfig.php```, которые вы найдете по следующему пути:

```bash
dir/engine/data
```
В файле ```config.php``` нужно отредактировать строку и поставить свой адрес (домен):
```php
'http_home_url' => 'https://espo.co.ua/',
```

В файле ```dbconfig.php``` нужно отредактировать несколько строк и указать данные для подключение БД:
```php
define ("DBHOST", "localhost");

define ("DBNAME", "your_DB_name");

define ("DBUSER", "your_DB_user");

define ("DBPASS", "your_DB_pass");
```

Затем вам нужно загрузить базу данных, которая находится по следующему пути:
```bash
your_dir/backup/db.sql
```

## Использование

Портал создан на CMS DataLifeEngine.

По пути ```dir/templates/Default``` вы можете редактировать шаблон. В файле ```main.tpl``` находится "скелет" шаблона.

В файле ```main.tpl``` вы сможете найти подобные строки:

```html
[available=main]
<!-- Блок "О нас" -->
{include file="modules/hero.tpl"}
<!-- Блок "Про гуртожитки" -->
{include file="modules/about.tpl"}
<!-- Блок с каруселью общежитий -->
{include file="modules/carousel.tpl"}
<!-- Блок рекламы поселения -->
{include file="modules/advert.tpl"}
[/available]
```

Как ни странно, файлы с этими блоками можно найти по следующему пути: ```dir/templates/Default/modules/...```

Для страницы каждого общежития созданы темплейты (файлы) с названием ```hostel-1.tpl``` и так далее.

Что бы попасть в админпанель допишите в конце адреса ```/admin.php```.
 В скрипте уже имеется пользователь, имя ```ADMIN```, пароль ```qwerty```


## Лицензия
[MIT](https://choosealicense.com/licenses/gpl-3.0/)
