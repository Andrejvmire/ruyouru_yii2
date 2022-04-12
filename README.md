
Установка
------------

Обновите зависимости

    docker-compose run --rm php composer update --prefer-dist


Конфигурирование
-------------

Отредактируйте файл `.env`, например:

```dotenv
MYSQL_ROOT_PASSWORD=root
MYSQL_DATABASE=db
MYSQL_USER=dev
MYSQL_PASSWORD=password

PHP_HOST=php

DB_HOST=mysql
DB_PORT=3306
TIMEZONE=Europe/Moscow
DB_CONNECTION=mysql:host=${DB_HOST}:${DB_PORT};dbname=${MYSQL_DATABASE}

JWT_KEY=secret
JWT_ISSUER=https://api.example.com
JWT_AUDIENCE=https://frontend.example.com
JWT_ID=UNIQUE-JWT-IDENTIFIER
JWT_EXPIRE=3000
```

Запуск
-------------
Запустите контейнер

    docker-compose up -d

Вы можете получить доступ в приложение отправив запрос по URL:

1. Регистрация.


    POST: http://127.0.0.1:80/user

отправляем:
```json
{
  "email": "exemple@some.post",
  "password": "******"
}
```
Получаем данные пользователя и токен

2. Данные пользователя (при наличии действующего токена)


    GET: http://127.0.0.1:80/user

Получаем данные пользователя

3. Авторизация:


    POST: http://127.0.0.1:80/user/login

отправляем:
```json
{
  "email": "exemple@some.post",
  "password": "******"
}
```
Получаем данные пользователя и токен

4. Дополняем профиль (при наличии действующего токена):


    POST: http://127.0.0.1:80/user/data

отправляем (все поля не обязательны):
```json
{
  "first_name": "Some",
  "last_name": "Some",
  "phone_number": "81234567890"
}
```
Получаем данные пользователя

Ответ
------
Данные пользователя содержат:
```json
{
    "user": {
      "id": 1,
      "email": "example@some.test",
      "first_name": "Some",
      "last_name": "Some",
      "phone_number": "81234567890"
    }
}
```
токен:
```json
{
  "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiIsImp0aSI6IlVOSVFVRS1KV1QtSURFTlRJRklFUiJ9.eyJpc3MiOiJodHRwczpcL1wvYXBpLmV4YW1wbGUuY29tIiwiYXVkIjoiaHR0cHM6XC9cL2Zyb250ZW5kLmV4YW1wbGUuY29tIiwianRpIjoiVU5JUVVFLUpXVC1JREVOVElGSUVSIiwiaWF0IjoxNjQ5NjAyMzQ5LCJleHAiOjE2NDk2MDI2NDksInVpZCI6MX0.gNfSenILQD93QtdntAz0ciPjUCOavZU5UpnToPMBdlE"
}
```