## Documentation, Installation, and Usage Instructions

Нужен: composer

Для разработки использовад Laravel Sail

После того как склонировали репу:

**Установить либы**
``` bash
composer i
```

**Запустить докер контейнер**
``` bash
vendor/bin/sail up -d
```

**Роут**
```http
GET /api/rate
```

| Parameter       | Type | Description                                       |
|:----------------| :--- |:--------------------------------------------------|
| `currency`      | `string` | **Required**. Symbol                              |
| `base_currency` | `string` | **Nullable**. Symbol (Default RUR)                |
| `date`          | `string` | **Nullable**. Date (Format: d/m/Y, deafult today) |



**Для запуска джобы по сбору исторических данных**
``` bash
vendor/bin/sail bash

php artisan migrate //миграции для создания таблицы для архивных данных

php artisan rates:parse {currency} {base_currensy=RUR} {n-days=180}

php artisan horizon //Запустить обработку джобы
```
Исторические данные можно посмотреть в таблице rates
