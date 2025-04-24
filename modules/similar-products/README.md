Похожие товары по опциям

## Вызов

```code
  {set $similar_products = "@FILE modules/similar-products/snippets/getSimilarProducts.php" | snippet : [
    'selection_option' => 'item_thickness',
    'main_options' => ['collection', 'item_width', 'item_length', 'produktovaya-lineyka'],
    'reserve_options' => ['plotnost'],
  ]}
```

## Доступные поля в JSON

```json
{
  "title": "Толщина:", // Название выборки - отображается над select
  "selection_option": "item_thickness", // Опция по которой будет выборка (список значений например толщин)
  "main_options": [
    "collection",
    "item_width",
    "item_length",
    "produktovaya-lineyka"
  ], // [НЕОБЯЗАТЕЛЬНО] Опции по которым будет поиск похожих товаров
  "reserve_options": ["plotnost"], // Опции по которым будет поиск похожих товаров если первой выборки будет не достаточно
  "unit": "ММ", // Единица измерения. Будет рядом с каджым значением в select
  "colored-tiles": false // Если true то на странице товара выведет плитки цветов
}
```
