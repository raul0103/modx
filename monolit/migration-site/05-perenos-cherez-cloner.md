# 5. Перенос на другую админку через CLONER

При переносе сайта на другую админку через компонент CLONER могут возникнуть дополнительные задачи.

## Получение шаблонов текущего сайта

Получаем список всех шаблонов, используемых в указанном контексте.

<details>
<summary>Показать код</summary>

```php
<?php

// создаём запрос
$query = $modx->newQuery('modResource');

// выбираем ID шаблона (template) и его имя (modTemplate.templatename)
$query->select([
    'modResource.template',
    'Template.templatename'
]);

// соединяем таблицу шаблонов
$query->leftJoin('modTemplate', 'Template', 'Template.id = modResource.template');

// фильтр по контексту
$query->where([
    'modResource.context_key' => 'zbi500',
]);

// уникальные template ID
$query->groupby('modResource.template');

// выполняем запрос
$query->prepare();
$query->stmt->execute();

// получаем пары ID => Название
$rows = $query->stmt->fetchAll(PDO::FETCH_ASSOC);

$result = [];
foreach ($rows as $row) {
    $result[$row['template']] = $row['templatename'];
}

print_r($result);

```

</details>

## Восстановление опций категорий

> ⚠️ **Проблема:** К категориям могут не привязаться все опции после переноса через CLONER.

### Шаг 1: Получение данных с оригинала

Выполните SQL-запрос на оригинальном сайте:

<details>
<summary>Показать код</summary>

```sql
SELECT mo.`key` AS option_key,msc.uri AS category_uri,mco.`rank` FROM modx_ms2_category_options AS mco
JOIN modx_ms2_options AS mo ON mo.id = mco.option_id
JOIN modx_site_content AS msc ON msc.id = mco.category_id
WHERE category_id IN (SELECT id FROM modx_site_content WHERE context_key = 'zbi500')
```

</details>

### Шаг 2: Восстановление на новом сайте

Запустите PHP-скрипт на новом сайте с полученными данными:

<details>
<summary>Показать код</summary>

```php
<?php

$items = [
  [
    'option_key' => 'razmer',
    'category_uri' => 'bruschatka-trotuarnaya',
    'rank' => 19,
  ],
  [
    'option_key' => 'obyem_m3',
    'category_uri' => 'aehrodromnye-plity',
    'rank' => 15,
  ],
  [
    'option_key' => 'obyem_m3',
    'category_uri' => 'plityi-dorozhnyie-2p',
    'rank' => 17,
  ],
  [
    'option_key' => 'obyem_m3',
    'category_uri' => 'plity-dorozhnye-1p',
    'rank' => 18,
  ],
];

foreach ($items as $item) {

  $option = $modx->getObject('msOption', [
    'key' => $item['option_key']
  ]);

  $category = $modx->getObject('modResource', [
    'uri' => $item['category_uri']
  ]);

  if (!$option || !$category) {
    echo "not found: {$item['option_key']} / {$item['category_uri']}" . PHP_EOL;
    continue;
  }

  $insertData = [
    'option_id'   => $option->get('id'),
    'category_id' => $category->get('id'),
    'rank'        => $item['rank'],
  ];

  $sql = "
    INSERT INTO modx_ms2_category_options
    (option_id, category_id, `rank`, active, required,value)
    VALUES (:option_id, :category_id, :rank, 1, 0, '')
  ";

  $stmt = $modx->prepare($sql);
  $result = $stmt->execute($insertData);

  if ($result) {
    echo "success: {$item['option_key']} -> {$item['category_uri']}" . PHP_EOL;
  } else {
    $errorInfo = $stmt->errorInfo();
    echo "error: " . print_r($errorInfo, true) . PHP_EOL;
  }
}

echo "done";

```

</details>

## Данные для msProductsComposerSelection и modx_ss_rules

> ⚠️ **Важно:** С оригинала сайта необходимо скопировать на новый всю таблицу `modx_products_composer_selection`.

### 🟢 СКРИПТ 1 — ДОНОР

Запустить на доноре для подготовки данных миграции.

<details>
<summary>Показать код</summary>

```php
 <?php
  /**
  * DONOR SCRIPT
  * Собирает:
  * 1. rid → uri
  * 2. parent IDs → uri
  */

  $context = 'fasad';
  $file = MODX_BASE_PATH . 'ms_products_composer_map.json';

  /* =========================
  * RID (msCategory)
  * ========================= */
  $categories = $modx->getCollection('modResource', [
      'context_key' => $context,
      'class_key'   => 'msCategory'
  ]);

  $rid = [];
  foreach ($categories as $cat) {
      $rid[$cat->id] = $cat->uri;
  }

  /* =========================
  * PARENT
  * ========================= */
  $res = $modx->query("
      SELECT val
      FROM modx_products_composer_selection
      WHERE `key` = 'parent'
  ");

  $parentsIds = [];
  foreach ($res->fetchAll(PDO::FETCH_ASSOC) as $row) {
      $parentsIds = array_merge($parentsIds, explode(',', $row['val']));
  }

  $parentsIds = array_unique(array_filter($parentsIds));

  $parentsResources = $modx->getCollection('modResource', [
      'id:in' => $parentsIds
  ]);

  $parents = [];
  foreach ($parentsResources as $res) {
      $parents[$res->id] = $res->uri;
  }

  /* =========================
  * SAVE
  * ========================= */
  $data = [
      'rid'    => $rid,
      'parent' => $parents
  ];

  file_put_contents($file, json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

  echo 'DONOR: success';

```

</details>

### 🔵 СКРИПТ 2 — НОВЫЙ САЙТ

Обновление данных в таблице `modx_products_composer_selection` на новом сайте.

<details>
<summary>Показать код</summary>

```php
<?php
/**
 * TARGET SCRIPT
 * Обновляет:
 * 1. rid
 * 2. parent
 */

$file = MODX_BASE_PATH . 'ms_products_composer_map.json';
$data = json_decode(file_get_contents($file), true);

if (!$data) {
    exit('JSON not found or invalid');
}

/* =========================
 * UPDATE RID
 * ========================= */
foreach ($data['rid'] as $oldId => $uri) {

    $resource = $modx->getObject('modResource', ['uri' => $uri]);
    if (!$resource) {
        continue;
    }

    $newId = $resource->id;

    $modx->query("
        UPDATE modx_products_composer_selection
        SET rid = {$newId}
        WHERE rid = {$oldId}
    ");
}

/* =========================
 * UPDATE PARENT
 * ========================= */
$res = $modx->query("
    SELECT id, val
    FROM modx_products_composer_selection
    WHERE `key` = 'parent'
");

$rows = $res->fetchAll(PDO::FETCH_ASSOC);

foreach ($rows as $row) {

    $parents = explode(',', $row['val']);
    $newParents = [];

    foreach ($parents as $oldParentId) {

        if (!isset($data['parent'][$oldParentId])) {
            continue;
        }

        $uri = $data['parent'][$oldParentId];
        $resource = $modx->getObject('modResource', ['uri' => $uri]);

        if ($resource) {
            $newParents[] = $resource->id;
        }
    }

    if (!empty($newParents)) {
        $ids = implode(',', array_unique($newParents));

        $modx->query("
            UPDATE modx_products_composer_selection
            SET val = '{$ids}'
            WHERE id = {$row['id']}
        ");
    }
}

echo 'TARGET: success';
```

</details>

### 🔵 СКРИПТ 3 — НОВЫЙ САЙТ

Обновление данных для плагина `modx_ss_rules` (плагин выводит "Вам могут понадобиться" табами в товаре).

<details>
<summary>Показать код</summary>

```php
<?php
/**
 * UPDATE modx_ss_rules.categories
 * old_id → uri → new_id
 */

$file = MODX_BASE_PATH . 'ms_products_composer_map.json';
$data = json_decode(file_get_contents($file), true);

if (!$data || empty($data['rid'])) {
    exit('Mapping file not found or invalid');
}

/* =========================
 * LOAD RULES
 * ========================= */
$res = $modx->query("
    SELECT id, categories
    FROM modx_ss_rules
    WHERE categories IS NOT NULL
      AND categories != ''
");

$rows = $res->fetchAll(PDO::FETCH_ASSOC);

/* =========================
 * UPDATE
 * ========================= */
foreach ($rows as $row) {

    $oldIds = explode(',', $row['categories']);
    $newIds = [];

    foreach ($oldIds as $oldId) {
        $oldId = trim($oldId);

        // old_id → uri
        if (!isset($data['rid'][$oldId])) {
            continue;
        }

        $uri = $data['rid'][$oldId];

        // uri → new_id
        $resource = $modx->getObject('modResource', ['uri' => $uri]);
        if ($resource) {
            $newIds[] = $resource->id;
        }
    }

    if (!empty($newIds)) {

        $newIds = implode(',', array_unique($newIds));

        $modx->query("
            UPDATE modx_ss_rules
            SET categories = '{$newIds}'
            WHERE id = {$row['id']}
        ");
    }
}

echo 'SS RULES: success';
```

</details>

