#Тестовое задание

Сервис для управления балансом пользователей с использованием Laravel и PostgreSQL.

## Функциональность

- Пополнение баланса
- Списание средств
- Переводы между пользователями
- Получение текущего баланса
- Транзакционность операций
- Валидация данных
- Обработка ошибок

## Технологии

- PHP 8.2
- Laravel 10
- PostgreSQL
- Docker & Docker Compose
- Nginx

## Установка и запуск
```bash
git clone <repository-url>
cd test
создаем .env, копируем содержимое .env.example
docker compose build
docker compose up -d
захлидим в контейнер php-fpm, выполняем команду - composer install
