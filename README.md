## Restaurant REST API

## Описание
Backend (REST API) часть сервиса для автоматизации работы ресторана. Сервис предоставляет функционал управления пользователями, категориями меню, блюдами и заказами.

## Реализованные возможности

### Пользователи
- Создание, редактирование, удаление (**Доступно только Super Admin**);
- Авторизация по email и паролю или пин-коду (**официанты только по пин-коду**);
- Сброс и восстановление пароля.

### Категории меню
- Создание, редактирование, удаление;
- Прикрепление изображений.

### Блюда
- Создание, редактирование, удаление;
- Привязка к категории меню;
- Прикрепление изображений.

### Заказы
- Создание, добавление блюд, удаление;
- Закрытие заказа;

### Безопасность
- Реализована аутентификация с помощью Laravel Sanctum;
- Реализовано разделение прав пользователей по ролям;
- Реализовано проверка прав на редактирование, удаление и закрытие заказа официантами;

### Роли пользователей
- Super Admin - управление пользователями, доступ ко всем функциям сервиса;
- Администратор - управление категориями блюд и блюдами, просмотр заказов и ежедневной отчетности;
- Официант - управление заказами.

## Технологический стек
- **PHP 8.2;**
- **Laravel 12.26.2;**
- **PostgreSQL 16.9;**
- **Nginx 1.29.1;**
- **Docker**
- **L5Swagger**

## Установка и запуск

1. Клонировать репозиторий
```shell
git clone https://github.com/pashidze/Restaurant-API.git
```

2. Установить зависимости
```shell
composer install
```

3. Скопировать и настроить .env
```shell
cp .env.example .env 
```

4. Запустить Docker контейнеры
```shell
docker compose build
docker compose up -d
```

5. Сгенерировать ключ приложения
```shell
docker compose exec backend php artisan key:generate
```

6. Запустить миграции и загрузку начальных данных
```shell
docker compose exec backend php artisan migrate --seed
```

7. Проверить доступность сервиса
```shell
http://localhost:8000/
```
### Дополнительно

1. Документация (swagger) доступна по ссылке
```shell
http://localhost:8000/api/documentation#/
```

2. Отчет за день формируется командой
```shell
docker compose exec backend php artisan app:day-report
```

3. Postman коллекция доступна по ссылке
```shell
https://pashidze-5779906.postman.co/workspace/My-Workspace~221f449a-d581-47f1-9a8e-5dd2a5a7330a/collection/47093217-5c922a6e-d94f-4cd0-a666-2955fe370aa3?action=share&source=copy-link&creator=47093217
```

## Возможные доработки
- Добавление подтверждения учетной записи;
- Создание feature тестов;
- Создание unit-теста artisan команд;
- Автоматизация формирования ежедневной отчетности.

## Основные эндпоинты

### Auth (Аутентификация и авторизация)
- ```POST /api/login``` - вход;
- ```POST /api/logout``` - выход;
- ```POST /api/logout_all``` - выход со всех устройств;
- ```POST /api/forgot-password``` - сброс пароля с отправкой ссылки для восстановления;
- ```POST /api/reset-password``` - восстановление пароля;

### User (Пользователь)
- ```GET /api/user``` - список;
- ```POST /api/user``` - добавление;
- ```GET /api/user/{id}``` - вывод одного;
- ```PATCH /api/user/{id}``` - редактировать;
- ```DELETE /api/user/{id}``` - удалить;

### Menu category (Категория меню)
- ```GET /api/menu_category``` - список;
- ```POST /api/menu_category``` - добавление;
- ```GET /api/menu_category/{id}``` - вывод одного;
- ```PATCH /api/menu_category/{id}``` - редактировать;
- ```DELETE /api/menu_category/{id}``` - удалить;

### Dish (Блюдо)
- ```GET /api/dish``` - список;
- ```POST /api/dish``` - добавление;
- ```GET /api/dish/{id}``` - вывод одного;
- ```PATCH /api/dish/{id}``` - редактировать;
- ```DELETE /api/dish/{id}``` - удалить;

### Dish (Блюдо)
- ```GET /api/order``` - список;
- ```POST /api/order``` - добавление;
- ```GET /api/order/{id}``` - вывод одного;
- ```PATCH /api/order/{id}``` - редактировать;
- ```PATCH /api/order/{id}/close``` - закрыть;
- ```DELETE /api/order/{id}``` - удалить;
