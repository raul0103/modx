# 1. Перенос файлов

Переносим без лишних тяжелых файлов. Пример:

<details>
<summary>Показать код</summary>

```bash
rsync -av \
  --exclude '_src/' \
  --exclude '_import/' \
  --exclude 'assets/template/img/import' \
  --exclude 'assets/template/img/pdf-to-jpg' \
  --exclude 'assets/cache_image' \
  --exclude 'assets/yandexmarket' \
  --exclude 'assets/images/products' \
  --exclude 'assets/images/imgWithWatermark' \
  --exclude 'assets/uploads' \
  --exclude 'core/cache' \
  --exclude 'core/backups' \
  --exclude 'core/cache-banners' \
  --exclude 'core/custom-cache' \
  /home/minvata/web/path/public_html/ \
  root@111.111.111.65:/home/user/web/new_path/public_html/
```

</details>

# 2. Чистка базы данных

Есть еще различные вспомогательные таблицы, которые тоже можно очистить

## Ресурсы

Удаление ресурсов по контекстам

<details>
<summary>Показать код</summary>

```sql
DELETE FROM modx_site_content WHERE context_key NOT IN ('tizol','beltep','baswool','xotpipe','isotecti','ruspanel')
```

Товары

```sql
DELETE FROM modx_ms2_products WHERE id NOT IN (SELECT id FROM modx_site_content)
```

TV

```sql
DELETE FROM modx_site_tmplvar_contentvalues WHERE contentid NOT IN (SELECT id FROM modx_site_content)
```

Опции

```sql
DELETE FROM modx_ms2_product_options WHERE product_id NOT IN (SELECT id FROM modx_site_content)
```

---

Опции категорий

```sql
DELETE FROM modx_ms2_category_options WHERE category_id NOT IN (SELECT id FROM modx_site_content)
```

Products INTRO

```sql
DELETE FROM modx_mse2_intro WHERE resource NOT IN (SELECT id FROM modx_site_content)
```

Галереи товаров

```sql
DELETE FROM modx_ms2_product_files WHERE product_id NOT IN (SELECT id FROM modx_site_content)
```

</details>

## Контексты

**Важно:** _не трогать **mgr,web**!_

<details>
<summary>Показать код</summary>

Лишние контексты

```sql
DELETE FROM modx_context WHERE `key` NOT IN ('web','mgr','tizol','beltep','baswool','xotpipe','isotecti','ruspanel')
```

Настройки контекстов

```sql
DELETE FROM modx_context_setting WHERE context_key NOT IN ('web','mgr','tizol','beltep','baswool','xotpipe','isotecti','ruspanel')
```

Разрешения

```sql
DELETE FROM modx_access_context WHERE `target` NOT IN ('web','mgr','tizol','beltep','baswool','xotpipe','isotecti','ruspanel')
```

</details>

# 3. Заливаем на боевой + доливаем доп файлы которые на 1м шаге не скачивали

- ../public_html/\_import/
- ../public_html/assets/template/img/import
- ../public_html/assets/template/img/pdf-to-jpg
- ../public_html/assets/template/img/chertezhi
- ../public_html/assets/template/img/instruction

Проверяем по базе если есть ТВ или в коде взаимодествие с файлами из данных папок, то пишем скрипты на перенос ТОЛЬКО НУЖНЫХ ФАЙЛОВ.

Например после чистки базы данная папка `../public_html/assets/template/img/chertezhi` не нужна, так как ресурсы и код ее не используют

```sql
SELECT * FROM modx_site_tmplvar_contentvalues WHERE `value` LIKE '%chertezhi%'
```

---

Но есть папки которые используются `- ../public_html/\_import/`
Часто от туда берутся картинки для ТВ полей в таком формате `_import/files/img-tizol-06-05-24/tizol-lajt.jpg`

Вообще неправильно брать изображение из папки \_import - Она временная
Поэтому необходимо написать скрипты для переноса всех картинок с `_import/files` например в `assets/images/tv`

Так как обычно это картинки для категорий, вот рабочий скрипт:

<details>
<summary>Показать код</summary>

```php
<?php

/**
 * Скрипт обновления картинок у msCategory
 * - берёт TV main_image
 * - качает картинку
 * - кладёт в /assets/images/categories/web/
 * - обновляет TV правильным путём
 */

$modx->getService('error', 'error.modError');
$modx->setLogLevel(modX::LOG_LEVEL_INFO);
$modx->setLogTarget('ECHO');

// Настройки
$tvName = 'mainImage'; // название ТВ поля в котором содержится путь к картинке
$baseUrl = 'https://minvata-78.ru/'; // Сайт от куда будем качать картинки
$savePath = MODX_BASE_PATH . 'assets/images/categories/'; // Путь куда качаем. Будет разбивка по контекстам

// Получаем все msCategory
$categories = $modx->getCollection('msCategory');
foreach ($categories as $cat) {
    $catId = $cat->get('id');
    $alias = $cat->get('alias');

    // Получаем значение TV
    $tvValue = $cat->getTVValue($tvName);
    if (empty($tvValue)) {
        $modx->log(modX::LOG_LEVEL_INFO, "[$catId] У категории нет TV $tvName");
        continue;
    }

    // Формируем URL к картинке
    $imgUrl = $baseUrl . ltrim($tvValue, '/');
    $ext = pathinfo($imgUrl, PATHINFO_EXTENSION);
    if (!$ext) $ext = 'jpg';

    // Имя файла = alias или id
    $filename = $alias ? $alias . '.' . $ext : $catId . '.' . $ext;
    $contextPath = $savePath . '/' . $cat->context_key;

    // Создадим папку если её нет
    if (!is_dir($contextPath)) {
        mkdir($contextPath, 0755, true);
    }

    // Качаем файл
    $imgData = @file_get_contents($imgUrl);
    if ($imgData === false) {
        $modx->log(modX::LOG_LEVEL_ERROR, "[$catId] Не удалось скачать: $imgUrl");
        continue;
    }
    file_put_contents($contextPath . '/' . $filename, $imgData);

    // Новый путь для ТВ
    $newTvPath = str_replace(MODX_BASE_PATH,'/', $contextPath . '/' . $filename);

    // Обновляем TV
    $cat->setTVValue($tvName, $newTvPath);

    $modx->log(modX::LOG_LEVEL_INFO, "[$catId] Обновлено: $newTvPath");
}

$modx->log(modX::LOG_LEVEL_INFO, "Готово!");

```

</details>

# 4. Галереи товаров

После переноса на сервер, необходимо импортнуть галереи из ../public_html/assets/images/products

1. Создать файл

<details>
<summary>Показать код</summary>

```txt
    + /assets/
    + /assets/images/
    + /assets/images/products/
    + /assets/images/products/12345/
    + /assets/images/products/10000/
    + /assets/images/products/54321/
    - *
```

</details>

2. Запустить скрипт

<details>
<summary>Показать код</summary>

```bash
rsync -av --include-from=include.txt \
/home/minvata/web/path/public_html/ \
root@193.42.125.65:/home/user/web/new_path/public_html/
```

</details>

3. Если ID товаров изменились то необходимо изменить названия папок и так же в modx_ms2_product_files. Можно получить uri с сайта оригинала, и по нему на новом сайте получать новый ID товара и менять старый ID На новый

---

# При переносе на другую админку через CLONER

Получение шаблонов текущего сайта

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

К категориям могут не привязаться все опции. Поэтому необходимо

1. Получить с оригинала

<details>
<summary>Показать код</summary>

```sql
SELECT mo.`key` AS option_key,msc.uri AS category_uri,mco.`rank` FROM modx_ms2_category_options AS mco
JOIN modx_ms2_options AS mo ON mo.id = mco.option_id
JOIN modx_site_content AS msc ON msc.id = mco.category_id
WHERE category_id IN (SELECT id FROM modx_site_content WHERE context_key = 'zbi500')
```

</details>

2. Массивом PHP прогнать на новом сайте

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

### Данные для msProductsComposerSelection и modx_ss_rules

С оригинала сайта скопировать на новый всю таблицу modx_products_composer_selection

#### 🟢 СКРИПТ 1 — ДОНOР

Запустить на доноре

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

#### 🔵 СКРИПТ 2 — НОВЫЙ САЙТ

Запускается на новом сайте

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

#### 🔵 СКРИПТ 3 - НОВЫЙ САЙТ

Данные для modx_ss_rules - Плагин который в товаре выводит "Вам могут понадобиться" табами

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

# Заключение

- Проверить:
  - Формы
  - robots
  - sitemap.xml
  - Политика конфиденциальности
