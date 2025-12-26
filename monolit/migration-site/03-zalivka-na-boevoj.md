# 3. Заливка на боевой сервер + дополнительные файлы

После переноса основных файлов необходимо долить дополнительные директории, которые были исключены на первом этапе.

> ⚠️ **Важно:** Перед переносом дополнительных файлов проверьте, используются ли они в базе данных или коде. Не переносите ненужные файлы!

## Дополнительные директории для переноса

- `../public_html/_import/`
- `../public_html/assets/template/img/import`
- `../public_html/assets/template/img/pdf-to-jpg`
- `../public_html/assets/template/img/chertezhi`
- `../public_html/assets/template/img/instruction`

## ⚠️ Важно: Проверка использования файлов

Перед переносом необходимо проверить, используются ли файлы из этих папок в базе данных или коде.

### Проверка через SQL

Проверяем наличие ссылок на файлы в TV-полях:

```sql
SELECT * FROM modx_site_tmplvar_contentvalues WHERE `value` LIKE '%chertezhi%'
```

> 💡 **Пример:** После чистки базы папка `../public_html/assets/template/img/chertezhi` может оказаться не нужна, так как ресурсы и код её не используют.

## Перенос изображений из _import

> ⚠️ **Проблема:** Часто изображения берутся из папки `_import`, что неправильно, так как это временная папка.

**Пример неправильного пути:** `_import/files/img-tizol-06-05-24/tizol-lajt.jpg`

### Решение

Необходимо написать скрипты для переноса всех картинок из `_import/files` в постоянную директорию, например `assets/images/tv` или `assets/images/categories`.

> 💡 **Рекомендация:** После переноса изображений обновите пути в TV-полях, чтобы они указывали на новые постоянные директории.

### Скрипт для категорий

Скрипт обновления картинок у msCategory:

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

## Сертификаты

### 🟢 СКРИПТ 1 — ДОНОР

Скрипт для сбора всех файлов из MIGX-TV "certs" у товаров контекста "fasad".

**Результат:** JSON-файл со списком уникальных путей.

<details>
<summary>Показать код</summary>

```php
<?php

/**
 * Сбор всех файлов из MIGX-TV "certs" у товаров контекста "fasad"
 * Результат: JSON-файл со списком уникальных путей
 */

$tvName = 'certs';
// Куда сохраняем JSON
$outputFile = MODX_BASE_PATH . 'all_certs.json';

// Собираем пути
$files = [];

$products = $modx->getCollection('msProduct', ['context_key' => 'fasad']);
foreach ($products as $product) {
    $certs = $product->getTVValue($tvName);
    if (empty($certs)) continue;

    $certsArray = json_decode($certs, true);
    if (!is_array($certsArray)) continue;

    foreach ($certsArray as $item) {
        if (!empty($item['file'])) {
            $files[] = $item['file'];
        }
    }
}

// Убираем дубли и сортируем
$files = array_values(array_unique($files));
sort($files);

// Сохраняем JSON
$json = json_encode($files, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
file_put_contents($outputFile, $json);

```

</details>

### 🔵 СКРИПТ 2 — НОВЫЙ САЙТ

Скрипт для скачивания сертификатов из JSON-массива.

<details>
<summary>Показать код</summary>

```php
<?php

/**
 * Скачивание сертификатов из JSON-массива
 */

// JSON-массив можно вставить прямо сюда
$json = '[
  "http://www-fasad.ru/assets/template/img/import//krovlyaspb/fasadnye_paneli_gl.pdf",

]';

$files = json_decode($json, true);

// Локальная папка
$savePath = MODX_BASE_PATH . 'assets/documents/certificates/';

// Создадим папку если её нет
if (!is_dir($savePath)) {
    mkdir($savePath, 0755, true);
}

foreach ($files as $file) {
    $url = $file;
    $filename = basename($file);
    $localFile = $savePath . $filename;


    // Скачивание
    $data = @file_get_contents($url);
    if ($data === false) {
        continue;
    }

    // Сохраняем локально
    file_put_contents($localFile, $data);
}

```

</details>

