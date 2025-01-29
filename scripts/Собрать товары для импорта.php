<?php

/**
 * Скрипт создает CSV файл в корне сайта, с данными по опциям товаров выбранного контекста
 * Об оптимизации не думал, накидал на коленке
 */

$limit = 10;
$offset = 0;
$context_key = "web";
$class_key = "msProduct";
$resources = []; // ID ресурсов для выборки, если пусто то не учитывается
$table_prefix = $modx->getOption('table_prefix');

//> Подключаем MODX
@include_once(dirname(dirname(__DIR__)) . '/config.core.php');
@include_once(dirname(dirname(__DIR__)) . '/core/model/modx/modx.class.php');

$modx = new modX();
$modx->initialize('mgr');
//<

/**
 * Массив ключ опции - название опции
 */
$options_result = $modx->query("SELECT `key`,caption FROM {$table_prefix}ms2_options");
$option_names = [];
while ($r = $options_result->fetch(PDO::FETCH_ASSOC)) {
    $option_names[$r['key']] = $r['caption'];
}

/**
 * Сбор товаров и их опций
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

$option_keys = [];
$product_template_ids = []; // ID Шаблонов. Для сбора ТВ полей привязанных к шаблону
$product_ids = []; // ID всех товаров для получения по ним картинок 
$product_options = []; // Чтобы повторно не грузить опции в коде ниже 
foreach ($products as $product) {
    $product_template_ids[] = $product->template;
    $product_ids[] = $product->id;
    $options = $product->loadData()->get('options');
    $product_options[$product->id] = $options;
    $option_keys = array_merge($option_keys, array_keys($options));
}
$option_keys = array_unique($option_keys);
$product_template_ids = array_unique($product_template_ids);

/**
 * Получаем все категории. По ним будем искать alias для каждого parent
 * Получаю их заранее что-бы не делать это в цикле
 */

$query = $modx->newQuery('modResource');
$query->select('id,alias');
$where = [
    'context_key' => $context_key,
    'class_key' => 'msCategory',
];
$query->where($where);
$result_categories = $modx->getCollection('modResource', $query);
$category_aliases = [];
foreach ($result_categories as $category) {
    $category_aliases[$category->id] =  $category->alias;
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
 * Сбор названий TV полей привязанных к шаблонам выбранных товаров
 */
$tv_result = $modx->query("
SELECT
    st.name
FROM
    {$table_prefix}site_tmplvar_templates AS stt
LEFT JOIN {$table_prefix}site_tmplvars AS st
ON
    st.id = stt.tmplvarid
WHERE
    stt.templateid IN (" . implode(',', $product_template_ids) . ")
");
$tv_names = [];
while ($r = $tv_result->fetch(PDO::FETCH_ASSOC)) {
    $tv_names[] = $r['name'];
}

/**
 * Формируем заголовки
 */
$header_names = ['ID', 'Название', 'longtitle', 'description', 'published', 'hidemenu', 'class_key', 'alias родителя', 'content'];
$header_keys =  ['id', 'pagetitle', 'longtitle', 'description', 'published', 'hidemenu', 'class_key', 'parent', 'content'];
foreach ($option_keys as $option_key) {
    $header_names[] = $option_names[$option_key];
    $header_keys[] = "options-" . $option_key;
}
foreach ($tv_names as $tv_name) {
    $header_names[] = $tv_name;
    $header_keys[] = "tv-" . $tv_name;
}

/**
 * Формируем результаты
 */
$value_rows = "";
foreach ($products as $product) {

    $value_rows .= $product->id . ';';
    $value_rows .= $product->pagetitle . ';';
    $value_rows .= $product->longtitle . ';';
    $value_rows .= $product->description . ';';
    $value_rows .= $product->published . ';';
    $value_rows .= $product->hidemenu . ';';
    $value_rows .= $product->class_key . ';';
    $value_rows .= $category_aliases[$product->parent] . ';';

    $value_rows .= '"' . preg_replace(['/\>[^\S ]+/s', '/[^\S ]+\</s', '/(\s)+/s', '/<!--(.|\s)*?-->/'], ['>', '<', '\\1', ''], $product->content) . '";';

    // Опции
    $options = $product_options[$product->id];
    foreach ($option_keys as $option_key) {
        $value_rows .= $options[$option_key][0] . ';';
    }

    // TV
    foreach ($tv_names as $tv_name) {
        $value_rows .= $product->getTVValue($tv_name) . ';';
    }

    // Картинки
    $images = $id_images[$product->id];
    foreach ($images as $image_idx => $image) {
        $image_key = "image-$image_idx";
        if (!in_array($image_key, $header_names)) {
            $header_names[] = $image_key;
            $header_keys[] = $image_key;
        }
        $value_rows .= $image . ';';
    }

    $value_rows .= PHP_EOL;
}

$header_names = implode(';', $header_names) . PHP_EOL;
$header_keys = implode(';', $header_keys) . PHP_EOL;
$output = $header_names . $header_keys . $value_rows;

/**
 * Сохраняем результат в файл
 */
$filename = "products_$offset.csv";
$full_path = MODX_BASE_PATH . $filename;
file_put_contents($full_path, $output);
