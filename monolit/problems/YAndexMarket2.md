### Проблема: Выводятся не все категории, только если сохранить то она вывоится

Решение:

- Находим файл `core\components\yandexmarket2\src\Processors\Categories\GetList.php`
- Меняем функцию `getResourceQuery`

```php
    public function getResourceQuery()
    {
        $c = parent::getResourceQuery();
        // $c->select([
        //     'childrenCount' => "(SELECT COUNT(*) FROM {$this->modx->getTableName('modResource')}"
        //         . " WHERE `parent` = `modResource`.`id` and `isfolder` = 1)",
        // ]);
        // $c->where(['isfolder' => true]);
        $c->where(['class_key' => 'msCategory']);

        return $c;
    }
```
