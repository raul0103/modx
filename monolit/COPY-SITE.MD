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
DELETE FROM modx_access_context WHERE context_key NOT IN ('web','mgr','tizol','beltep','baswool','xotpipe','isotecti','ruspanel')
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
