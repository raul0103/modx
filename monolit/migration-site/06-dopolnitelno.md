# 6. Дополнительные данные (если ID изменились)

> ⚠️ **Важно:** Этот раздел актуален **только если ID ресурсов изменились** после переноса. Если ID остались прежними, этот раздел можно пропустить.

## Когда использовать этот раздел

Используйте скрипты из этого раздела, если:
- Вы переносили сайт через CLONER и ID ресурсов изменились
- В таблицах `modx_products_composer_selection` и `modx_ss_rules` остались старые ID
- Необходимо обновить связи между ресурсами

Если ID не изменились, просто скопируйте таблицы с оригинала и пропустите этот раздел.

---

## Данные для msProductsComposerSelection и modx_ss_rules

Если были изменения ID на сайте, необходимо изменить данные таблицы.

> ⚠️ **Важно:** С оригинала сайта необходимо скопировать на новый всю таблицу `modx_products_composer_selection` перед выполнением скриптов обновления.

### 🟢 СКРИПТ 1 — ДОНОР

Запустить на доноре для подготовки данных миграции. Создает JSON-файл с маппингом старых ID → URI.

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

Обновление данных в таблице `modx_products_composer_selection` на новом сайте. Использует URI для сопоставления старых и новых ID.

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

## Проверка после выполнения

После выполнения всех скриптов проверьте:

- [ ] Таблица `modx_products_composer_selection` содержит корректные ID
- [ ] Таблица `modx_ss_rules` содержит корректные ID категорий
- [ ] Функционал "Вам могут понадобиться" работает корректно
- [ ] Все связи между ресурсами восстановлены
