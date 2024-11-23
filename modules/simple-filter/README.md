## Описание

Запросы строятся по уникальному ключу например $filter_uniqueid = 'filter1'.

Благодаря этому можно вызвать несколько фильтров на странице

```code
?filter1={"filters":{"dlina-mm":["930"]},"page":1}
```

## Вывод

```code
{'@FILE modules/simple-filter/snippets/getFilters.php' | snippet : [
	'parents' => 17
	'tpl' => '@FILE modules/simple-filter/chunks/filters.tpl'
	'filter_uniqueid' => 'filter1'
	'options' => ['dlina-mm','shirina-mm','vysota-mm','massa-t']
]}
{'@FILE modules/simple-filter/snippets/getProducts.php' | snippet : [
	'parents' => 17
	'options' => ['dlina-mm','shirina-mm','vysota-mm','massa-t']
	'filter_uniqueid' => 'filter1'
	'tpl' => '@FILE modules/simple-filter/chunks/products-outer.tpl'
]}
```

##

- директорию connectors перенести в assets/
- Вызвать скрипты js/main.js init()
