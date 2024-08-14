# Guest Microservice

## Описание

Микросервис для управления данными гостей. Реализован CRUD с валидацией данных. Если страна не указана, она автоматически определяется по номеру телефона. Написан на PHP (Lumen) и MySQL. Микросервис работает в Docker. В ответах сервера присутствуют заголовки X-Debug-Time и X-Debug-Memory.

## Установка

1. Клонировать репозиторий:
    ```bash
    git clone roman2959/guest-microservice
    cd guest-microservice
    ```

2. Скопировать файл `.env.example` в `.env` и настроить его:
    ```bash
    cp .env.example .env
    ```

3. Установить зависимости:
    ```bash
    composer install
    ```

4. Собрать и запустить контейнеры:
    ```bash
    docker-compose up -d --build
    ```

5. Выполнить миграции базы данных:
    ```bash
    docker-compose exec app php artisan migrate
    ```

## Запуск тестов

Для запуска тестов:
```bash
docker-compose exec app vendor/bin/phpunit
```

## API Эндпоинты

### 1. Получить список гостей

**GET /api/guests**

Возвращает список всех гостей.

**Пример запроса:**
```bash
curl -X GET http://localhost:8000/api/guests
```

**Пример ответа:**
```json
[
    {
        "id": 1,
        "first_name": "John",
        "last_name": "Doe",
        "email": "john.doe@example.com",
        "phone": "+79001234567",
        "country": "RU",
        "created_at": "2024-08-14T11:08:48.000000Z",
        "updated_at": "2024-08-14T11:08:48.000000Z"
    }
]
```

### 2. Получить гостя по ID

**GET /api/guests/{id}**

Возвращает данные гостя с указанным ID.

**Пример запроса:**
```bash
curl -X GET http://localhost:8000/api/guests/1
```

**Пример ответа:**
```json
{
    "id": 1,
    "first_name": "John",
    "last_name": "Doe",
    "email": "john.doe@example.com",
    "phone": "+79001234567",
    "country": "RU",
    "created_at": "2024-08-14T11:08:48.000000Z",
    "updated_at": "2024-08-14T11:08:48.000000Z"
}
```

### 3. Создать нового гостя

**POST /api/guests**

Создает нового гостя.

**Параметры:**
- `first_name` (string, обязательный) - Имя гостя
- `last_name` (string, обязательный) - Фамилия гостя
- `email` (string, обязательный) - Email гостя (уникальный)
- `phone` (string, обязательный) - Номер телефона гостя (уникальный)
- `country` (string, необязательный) - Страна гостя (если не указана, определяется автоматически по номеру телефона)

**Пример запроса:**
```bash
curl -X POST http://localhost:8000/api/guests \
-H "Content-Type: application/json" \
-d '{"first_name": "John", "last_name": "Doe", "email": "john.doe@example.com", "phone": "+79001234567"}'
```

**Пример ответа:**
```json
{
    "id": 1,
    "first_name": "John",
    "last_name": "Doe",
    "email": "john.doe@example.com",
    "phone": "+79001234567",
    "country": "RU",
    "created_at": "2024-08-14T11:08:48.000000Z",
    "updated_at": "2024-08-14T11:08:48.000000Z"
}
```

### 4. Обновить данные гостя

**PUT /api/guests/{id}**

Обновляет данные гостя с указанным ID.

**Параметры:**
- `first_name` (string, необязательный) - Имя гостя
- `last_name` (string, необязательный) - Фамилия гостя
- `email` (string, необязательный) - Email гостя (должен быть уникальным)
- `phone` (string, необязательный) - Номер телефона гостя (должен быть уникальным)
- `country` (string, необязательный) - Страна гостя

**Пример запроса:**
```bash
curl -X PUT http://localhost:8000/api/guests/1 \
-H "Content-Type: application/json" \
-d '{"first_name": "Jane"}'
```

**Пример ответа:**
```json
{
    "id": 1,
    "first_name": "Jane",
    "last_name": "Doe",
    "email": "john.doe@example.com",
    "phone": "+79001234567",
    "country": "RU",
    "created_at": "2024-08-14T11:08:48.000000Z",
    "updated_at": "2024-08-14T11:12:48.000000Z"
}
```

### 5. Удалить гостя

**DELETE /api/guests/{id}**

Удаляет гостя с указанным ID.

**Пример запроса:**
```bash
curl -X DELETE http://localhost:8000/api/guests/1
```

**Пример ответа:**
```json
{
    "message": "Guest deleted"
}
```
