# 1. Перенос файлов

Переносим без лишних тяжелых файлов. Пример:

- ../public_html/assets/images
- ../public_html/assets/template/img/import
- ../public_html/assets/template/img/pdf-to-jpg
- ../public_html/assets/template/img/chertezhi
- ../public_html/assets/template/img/instruction

# 2. Чистка базы данных

Есть еще различные вспомогательные таблицы, которые тожэе можно очистить

## Ресурсы

Удаление ресурсов по контекстам

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

---

## Контексты

**Важно:** _не трогать **mgr,web**!_

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

Но есть папки которые исопльзуются `- ../public_html/\_import/`
Часто от туда берутся картинки для ТВ полей в таком формате `_import/files/img-tizol-06-05-24/tizol-lajt.jpg`

Вообще неправильно брать изображение из папки \_import - Она временная
Поэтому необходимо написать скрипты для переноса всех картинок с `_import/files` например в `assets/images/tv`

Так как обычно это картинки для категорий, вот рабочий скрипт:

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

# 4. Галереи товаров

После переноса на сервер, необходимо импортнуть галереи

- ../public_html/assets/images

Получить картинки

```php
<?php

/**
 * Скрипт создает CSV файл в корне сайта, с данными по опциям товаров выбранного контекста
 * Об оптимизации не думал, накидал на коленке
 */

$limit = 5000;
$offset = 0;
$context_key = "metallprofil";
$domain = "www-knauf.ru";
$class_key = "msProduct";
$resources = []; // ID ресурсов для выборки, если пусто то не учитывается
$table_prefix = $modx->getOption('table_prefix');

//> Подключаем MODX
// @include_once(dirname(dirname(__DIR__)) . '/config.core.php');
// @include_once(dirname(dirname(__DIR__)) . '/core/model/modx/modx.class.php');

// $modx = new modX();
// $modx->initialize('mgr');
//<



/**
 * Сбор товаров
 */
$query = $modx->newQuery('msProduct');
$query->limit($limit, $offset); // limit, offset
$where = [
    'context_key' => $context_key,
    'class_key' => $class_key,
];
if (!empty($resources)) {
    $where['id:in'] = $resources;
}
$query->where($where);
$products = $modx->getCollection('msProduct', $query);

$product_ids = []; // ID всех товаров для получения по ним картинок

foreach ($products as $product) {
    $product_ids[] = $product->id;
}

/**
 * Сбор картинок
 */
$images_result = $modx->query("
SELECT
    pf.url,
    sc.id
FROM
    {$table_prefix}ms2_product_files AS pf
LEFT JOIN {$table_prefix}site_content AS sc
ON
    sc.id = pf.product_id
WHERE
    pf.parent = 0 AND sc.id IN (" . implode(',', $product_ids) . ")");
$id_images = [];
while ($r = $images_result->fetch(PDO::FETCH_ASSOC)) {
    $id = $r['id'];
    if (empty($id_images[$id])) $id_images[$id] = [];
    $id_images[$id][] = $r['url'];
}

/**
 * Формируем заголовки
 */
$header_keys =  ['alias', 'parent'];

/**
 * Формируем результаты
 */
$value_rows = "";
foreach ($products as $product) {
    $value_rows .= $product->alias . ';';
    $value_rows .= $product->parent . ';';

    // Картинки
    $images = $id_images[$product->id];
    foreach ($images as $image_idx => $image) {
        $image_key = "image-$image_idx";
        if (!in_array($image_key, $header_keys)) {
            $header_keys[] = $image_key;
        }
        $value_rows .= "https://$domain/$image" . ';';
    }

    $value_rows .= PHP_EOL;
}

$header_keys = implode(';', $header_keys) . PHP_EOL;
$output = $header_keys . $value_rows;

/**
 * Сохраняем результат в файл
 */
$filename = "images.csv";
$full_path = MODX_BASE_PATH . $filename;
file_put_contents($full_path, $output);

```

На новом сайте - создать папку \_import-images в ней файлы для импорта
Импортируем на новый сайт
Если запускать не через консоль то раскомментировать подключение modx

```php
<?php

/**
 * Импорт изображений в miniShop2 из CSV (с поддержкой нескольких изображений)
 */

header('Content-Type: text/html; charset=utf-8');
ini_set("max_execution_time", 0);

define('MODX_API_MODE', true);

$csvFile = MODX_BASE_PATH . '/_import-images/images.csv';
// $parent_dir = dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR;
// $index_php = $parent_dir . 'index.php';

// $i = 0;
// while (!file_exists($index_php) && $i < 9) {
//     $parent_dir = dirname(dirname($index_php)) . '/';
//     $index_php = $parent_dir . 'index.php';
//     $i++;
// }

// if (file_exists($index_php)) {
//     require_once $index_php;
// }

// if (!is_object($modx)) {
//     exit('Ошибка при попытке подгрузить MODX');
// }

if (!$minishop2 = $modx->getService('minishop2')) return;

// Функция для загрузки изображения
function download_image($url, $save_path)
{
    $dir = dirname($save_path);
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }

    $image_data = file_get_contents($url);
    if ($image_data === false) {
        echo "Ошибка загрузки: $url\n";
        return false;
    }

    file_put_contents($save_path, $image_data);
    return true;
}

// Открытие CSV

$handle = fopen($csvFile, 'r');
if ($handle === false) {
    die("Ошибка открытия файла $csvFile");
}

$images = [];

$header = fgetcsv($handle, 1000, ';'); // первая строка - заголовок

while (($data = fgetcsv($handle, 1000, ';')) !== false) {
    $alias = $data[0];
    $parent = $data[1];

    $key = $alias.'-'.$parent;
    $images[$key] = [];

    for ($i = 2; $i < count($data); $i++) {
        $url = trim($data[$i]);
        if (!empty($url)) {
            $images[$key][] = $url;
        }
    }
}
fclose($handle);

$temp_images_dir = MODX_BASE_PATH . '/_import-images/temp_images/';
$log_file = MODX_BASE_PATH . '/_import-images/images.log';

$products = $modx->getIterator('modResource', [
    'class_key' => 'msProduct'
]);

foreach ($products as $product) {
    $key = $product->alias.'-'.$product->parent;

    if (isset($images[$key])) {
        foreach ($images[$key] as $image_url) {
            $path_parts = parse_url($image_url, PHP_URL_PATH);
            $file_name = basename($path_parts);
            $save_path = $temp_images_dir . $product->id . '/' . $file_name;

            if (download_image($image_url, $save_path)) {
                $message = $product->id . ' | ' . $product->alias . ' | ' . $product->parent . ' | ' . $file_name . "\n";
                echo $message;
                file_put_contents($log_file, $message, FILE_APPEND);

                $res = $minishop2->runProcessor('mgr/gallery/upload', [
                    'id' => $product->id,
                    'file' => $save_path
                ]);

                if ($res->isError()) {
                    file_put_contents($log_file, "Ошибка загрузки в miniShop2: " . print_r($res->getAllErrors(), true) . "\n", FILE_APPEND);
                }
            } else {
                $message = "❌ Не удалось сохранить изображение: $image_url\n";
                echo $message;
                file_put_contents($log_file, $message, FILE_APPEND);
            }
        }
    }
}
```

---

# При переносе на другую админку через CLONER

Получение шаблонов текущего сайта

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

К категориям могут не привязаться все опции. Поэтому необходимо

1. Получить с оригинала

```sql
SELECT mo.`key` AS option_key,msc.alias AS category_alias,mco.`rank` FROM modx_ms2_category_options AS mco
JOIN modx_ms2_options AS mo ON mo.id = mco.option_id
JOIN modx_site_content AS msc ON msc.id = mco.category_id
WHERE category_id IN (SELECT id FROM modx_site_content WHERE context_key = 'zbi500')
```

2. Массивом PHP прогнать на новом сайте

```php
<?php

$items = [
  [
    'option_key' => 'razmer',
    'category_alias' => 'bruschatka-trotuarnaya',
    'rank' => 19,
  ],
  [
    'option_key' => 'obyem_m3',
    'category_alias' => 'aehrodromnye-plity',
    'rank' => 15,
  ],
  [
    'option_key' => 'obyem_m3',
    'category_alias' => 'plityi-dorozhnyie-2p',
    'rank' => 17,
  ],
  [
    'option_key' => 'obyem_m3',
    'category_alias' => 'plity-dorozhnye-1p',
    'rank' => 18,
  ],
];

foreach ($items as $item) {

  $option = $modx->getObject('msOption', [
    'key' => $item['option_key']
  ]);

  $category = $modx->getObject('modResource', [
    'alias' => $item['category_alias']
  ]);

  if (!$option || !$category) {
    echo "not found: {$item['option_key']} / {$item['category_alias']}" . PHP_EOL;
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
    echo "success: {$item['option_key']} -> {$item['category_alias']}" . PHP_EOL;
  } else {
    $errorInfo = $stmt->errorInfo();
    echo "error: " . print_r($errorInfo, true) . PHP_EOL;
  }
}

echo "done";

```

### Данные для msProductsComposerSelection и modx_ss_rules

С оригинала сайта скопировать на новый всю таблицу modx_products_composer_selection

#### 🟢 СКРИПТ 1 — ДОНOР

Запустить на доноре

```php
 <?php
  /**
  * DONOR SCRIPT
  * Собирает:
  * 1. rid → alias
  * 2. parent IDs → alias
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
      $rid[$cat->id] = $cat->alias;
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
      $parents[$res->id] = $res->alias;
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

#### 🔵 СКРИПТ 2 — НОВЫЙ САЙТ

Запускается на новом сайте

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
foreach ($data['rid'] as $oldId => $alias) {

    $resource = $modx->getObject('modResource', ['alias' => $alias]);
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

        $alias = $data['parent'][$oldParentId];
        $resource = $modx->getObject('modResource', ['alias' => $alias]);

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

#### 🔵 СКРИПТ 3 - НОВЫЙ САЙТ

Данные для modx_ss_rules - Плагин который в товаре выводит "Вам могут понадобиться" табами

```php
<?php
/**
 * UPDATE modx_ss_rules.categories
 * old_id → alias → new_id
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

        // old_id → alias
        if (!isset($data['rid'][$oldId])) {
            continue;
        }

        $alias = $data['rid'][$oldId];

        // alias → new_id
        $resource = $modx->getObject('modResource', ['alias' => $alias]);
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

# Заключение

- Проверить:
  - Формы
  - robots
  - sitemap.xml
  - Политика конфиденциальности
