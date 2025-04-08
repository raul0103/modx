## Логика

- Создает таблицу в БД для записи в нее заявок с обратной формы
- По крону скрипт `/queue_callback/cron/index.php` проходит таблицу и проверяет
  - Если есть не отправленные сообщения `sent=0` и их дата превышает 10 минут, то отправить сообщение `message` на `url`

## Установка

1. Запустить snippets/install.php
   - Будут установлены модели
   - Создается таблица в БД
2. Установить крон задание (можно раз в 5 минут)

```code
/queue_callback/cron/index.php
```

## Запуск

1. Вызвать в любом скрипте

```php
include MODX_CORE_PATH . '/elements/modules/queue_callback/snippets/qc.class.php';
$qc = new QC();
/**
 * Если есть прошлое не отправленно сообщение обновляем его
 * else - Создаем новую запись
 */
if ($qc->updateRecord($data)) {
    return;
} else {
    $qc->setRecord($hook_url, json_encode($data, JSON_UNESCAPED_UNICODE));
}
```
