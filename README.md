# Тестовое задание Яршинторг

Есть таблица с товарами, у каждого товара есть Артикул и Название  
Нужно сделать API для получения списка товаров с постраничной навигацией и возможностью поиска сразу по двум полям Артикул и Название
 
для реализации API использовать https://api-platform.com/  
точка вызова должна выглядеть примерно так: /api/products/?search=<Артикул или Название>  
Артикул: /api/products/?search=p-1234567  
Название: /api/products/?search=pirelli  
 
для фронтенда использовать https://vuetifyjs.com  
отображения списка товаров через компонент https://vuetifyjs.com/en/components/data-tables/  
над таблицей должно быть поле строки поиска  
поиск начинается автоматичесrи после завершения ввода текста

## Demo

[Front](https://ytt.tecpremi.ru)
[Back](https://api-ytt.tecpremi.ru)

## Install with Docker

Установка

    docker-compose build

Запуск

    docker-compose up -d

После запуска при необходимости создать базу и выполнить миграции

    docker-compose exec php php bin/console doctrine:schema:create
    docker-compose exec php php bin/console doctrine:migrations:migrate

Так же для тестового окружения

    docker-compose exec php php bin/console --env=test doctrine:schema:create
    docker-compose exec php php bin/console --env=test doctrine:migrations:migrate

Запуск тестов

    docker-compose exec php php bin/phpunit

Остановка

    docker-compose stop
