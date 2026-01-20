# Описание

Assignments Form - это тестовое задание, выполненное на фреймворке Laravel. Представляет собой регистрационную форму пользователя с простой валидацией, логированием и стандартными сообщениями об ошибках.

ТЗ: https://greensight.notion.site/Backend-f863a6666e9f40f99f41254a1fffe450

## Минимальные требования

- Ubuntu Linux
- PHP 8.2+
- Composer
- Node.js + npm
- SQLite (default) или другая БД, сконфигурированная в `.env`

## Установка

```bash
git clone git@github.com:Kirill070/assignments-form-test.git
cd assignments-form-test
cp .env.example .env
composer install
php artisan key:generate
php artisan migrate --seed
npm install
```

## Запуск (dev)

Terminal 1:

```bash
npm run dev
```

Terminal 2:

```bash
php artisan serve
```

## Запуск (prod)

```bash
npm run build
php artisan serve
```

## Тесты

```bash
php artisan test
```

## Примечание

- Запись логов осуществляется в `storage/logs/registration.log` (не коммитится в гит).
- Сидер: `database/seeders/DatabaseSeeder.php`.
