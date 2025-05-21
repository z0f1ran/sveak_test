# Приложение для Скоринга Клиентов

Простое веб-приложение на Symfony для регистрации клиентов и расчета их скоринга.

## Требования к окружению

*   PHP 8.2
*   Composer
*   MySQL сервер
*   Веб-сервер (например, Symfony CLI, Nginx, Apache) или возможность запуска встроенного PHP веб-сервера.

## Установка и Настройка

1.  **Клонируйте репозиторий**

2.  **Перейдите в корневую директорию проекта:**
    ```bash
    cd path/to/sveak_project
    ```

3.  **Установите зависимости PHP:**
    ```bash
    composer install
    ```

4.  **Настройте подключение к базе данных:**
    Откройте файл `.env` и измените строку `DATABASE_URL` для вашего MySQL сервера:
    Пример:
    ```env
    DATABASE_URL="mysql://root:root@127.0.0.1:3306/sveak_scoring?serverVersion=8.0&charset=utf8mb4"
    ```
    Замените `root:root` на ваши имя пользователя и пароль, `127.0.0.1:3306` на ваш хост и порт, `sveak_scoring` на желаемое имя БД, и `serverVersion` на вашу версию MySQL.

5.  **Создайте базу данных** (если она еще не создана) с именем, указанным в `DATABASE_URL`.
    Например, в MySQL клиенте:
    ```sql
    CREATE DATABASE sveak_scoring CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    ```

6.  **Выполните миграции базы данных** для создания таблицы `client`:
    ```bash
    php bin/console doctrine:migrations:migrate
    ```
    На вопрос о выполнении ответьте `yes`.

7.  **(Опционально) Загрузите тестовые данные (фикстуры):**
    Это создаст несколько тестовых клиентов в базе данных.
    ```bash
    php bin/console doctrine:fixtures:load
    ```
    На вопрос об очистке базы данных и продолжении ответьте `yes`.

## Запуск Приложения

Рекомендуется использовать Symfony CLI:

1.  **Установите Symfony CLI:** [https://symfony.com/download](https://symfony.com/download)
2.  **Запустите веб-сервер:**
    ```bash
    symfony server:start
    ```
    Эта команда покажет URL, по которому будет доступно приложение (обычно `https://127.0.0.1:8000`).

**Основные страницы:**
*   Регистрация нового клиента: `/register`
*   Список клиентов: `/clients`

## Запуск Тестов

Для запуска PHPUnit тестов выполните команду в корневой директории проекта:
```bash
php bin/phpunit
```

## Доступные Консольные Команды

*   **Пересчет скоринга:**
    *   Для всех клиентов: `php bin/console app:recalculate-scoring`
    *   Для одного клиента (например, с ID=1): `php bin/console app:recalculate-scoring 1` 