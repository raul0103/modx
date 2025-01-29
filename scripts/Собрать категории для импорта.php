<?php

$limit = 10;
$offset = 0;
$context_key = "web";
$class_key = "msCategory";
$table_prefix = $modx->getOption('table_prefix');

$query = $modx->newQuery('modResource');
$query->limit($limit, $offset); // limit, offset
$where = [
    'context_key' => $context_key,
    'class_key' => $class_key,
];
$query->where($where);
$categories = $modx->getCollection('modResource', $query);

$categories_template_ids = [];
foreach ($categories as $category) {
    $categories_template_ids[] = $category->template;
}

/**
 * Сбор названий TV полей привязанных к шаблонам
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
    stt.templateid IN (" . implode(',', $categories_template_ids) . ")
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

foreach ($tv_names as $tv_name) {
    $header_names[] = $tv_name;
    $header_keys[] = "tv-" . $tv_name;
}

/**
 * Формируем результаты
 */
$value_rows = "";
foreach ($categories as $category) {

    $value_rows .= $category->id . ';';
    $value_rows .= $category->pagetitle . ';';
    $value_rows .= $category->longtitle . ';';
    $value_rows .= $category->description . ';';
    $value_rows .= $category->published . ';';
    $value_rows .= $category->hidemenu . ';';
    $value_rows .= $category->class_key . ';';
    $value_rows .= $category_aliases[$category->parent] . ';';

    $value_rows .= '"' . preg_replace(['/\>[^\S ]+/s', '/[^\S ]+\</s', '/(\s)+/s', '/<!--(.|\s)*?-->/'], ['>', '<', '\\1', ''], $category->content) . '";';

    // TV
    foreach ($tv_names as $tv_name) {
        $value_rows .= $category->getTVValue($tv_name) . ';';
    }

    $value_rows .= PHP_EOL;
}

$header_names = implode(';', $header_names) . PHP_EOL;
$header_keys = implode(';', $header_keys) . PHP_EOL;
$output = $header_names . $header_keys . $value_rows;

/**
 * Сохраняем результат в файл
 */
$filename = "categories_$offset.csv";
$full_path = MODX_BASE_PATH . $filename;
file_put_contents($full_path, $output);
