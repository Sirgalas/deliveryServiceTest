## Тестовое задание по разработке сервиса курьерской доставки

### Задача

Реализовать сервис курьерской доставки для покупателей интернет-магазинов.

### Описание бизнес-логики

1. Интернет-магазин делает запрос в сервис доставки на создание заказа.
2. Сервис доставки регистрирует заказ и возвращает свой UUID для отображения пользователю интернет-магазина.
3. Интернет-магазин может сделать запрос на отмену заказа или получение текущего статуса доставки.

### Технические требования

1. Разработать RESTFul API сервиса доставки согласно [спецификации](specification.yaml).
2. `API` должен позволять делать:
    - `POST /order`: Создание заказа на доставку.
    - `DELETE /order/{id}`: Отмену заказа.
    - `GET /order/{id}`: Получение статуса заказа.
3. Допускается использование любого PHP фреймворка из списка:
    - Yii2
    - Symfony
    - Laravel
    - Slim

### Общие требования

1. Проект следует разместить в отдельном публичном репозитории на `GitHub` или `Bitbucket`.
2. Развертывание проекта должно осуществляться с использованием `Docker` контейнеров.
3. Проект должен включать инструкции и скрипты для его развертывания в `DEV` окружении.
4. Стартовые миграции базы данных должны выполняться автоматически.

### Дополнительные задачи (по желанию)

1. Реализуйте аутентификацию и авторизацию запросов, используя JWT согласно `спецификации`.
2. Создайте автоматические модульные (unit) тесты для проверки корректности работы API.
3. Добавьте файловое логирование событий, связанных с созданием и удалением заказов с учетом, что целевая нагрузка на систему - 10 заказов в секунду, 24/7.

### Дополнительные задачи для экспертов (по желанию)

#### Отдельный сервис аутентификации и авторизации

1. Добавьте в реализацию отдельный сервис аутентификации и авторизации.
2. Сервис должен выдавать короткоживущие многоразовые токены доступа `JWT` и долгоживущие одноразовые `refresh token`.
3. Решение должно минимизировать последствия кражи токенов доступа и снижать количество запросов к БД.
4. При этом клиенты не должны часто "отлогиниваться" или испытывать какой-либо дискомфорт.

#### Определение времени и стоимости доставки

1. Реализуйте методы определения времени и стоимости доставки.
2. В целях расчёта времени доставки, вам понадобится использовать один из сервисов гео локации для определения расстояния между двумя точками на карте.
3. Для получения данных, позволяющих определить стоимость доставки:
    - Необходимо доработать метод `POST /order` сервиса доставки путём расширения `спецификации`.
    - Для примера, можно добавить требование к интернет-магазинам передавать параметры, необходимые для расчёта стоимости.
    - В простом случае, стоимость доставки можно посчитать как 5% от общей суммы заказа.