- Вызвать скрипт

```php
{set $unit_price = "@FILE modules/split-unit/snippets/splitUnit.php" | snippet [
    'formula' => 'web'
]}
```

- В листинге товаров необходимо передать параметры! Так как они не доступны через $modx->resource->get

```php
{set $split_unit_items = "@FILE modules/split-unit/snippets/splitUnit.php" | snippet : [
  'formula' => 'web',
  'fields' => [
    'unit' => $unit,
    'price' => $price,
    'k_m2seam' => $_pls['k_m2seam'],
    'k_m3seam' => $_pls['k_m3seam']
  ]
]}
```

- Формулы 'formula' => 'web', тут split-unit/snippets/formulas
- Получаем данные формата

```json
[
  { "unit": "шт.", "value": 55 },
  { "unit": "м3", "value": 29095 }
]
```

- На странице вывести кнопки с обязательными параметрами
  - data-split-unit-product

```html
{foreach $split_unit_items as $index => $item}
<button
  onclick="split_unit.events.activation({$id ?: $_modx->resource.id},'{$item['unit']}',this)"
  class="{if $index == 0}active{/if}"
>
  {$item['unit']}
</button>
{/foreach}
```

- На странице вывести цены с обязательными параметрами
  - data-split-unit-product
  - data-split-unit

```html
{foreach $split_unit_items as $index => $item}
<div
  data-split-unit-product="{$product_id}"
  data-split-unit="{$item['unit']}"
  class="{if $index == 0}opened{/if}"
>
  {$item['value']}
</div>
{/foreach}
```

- Вызвать скрипт scripts/js/main.js

  - Данный скрипт создает глобальные функции для управления
  - Назначает кнопкам класс .active, элементам с ценой .opened

- Вызвать стили scss/main.scss
