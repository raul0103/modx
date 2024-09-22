<?php

/**
 * Грубоватый скрипт, требует оптимизации
 */
$thumb_folder = "small/"; // Папка от куда будет получена уменьшенная первая картинка
$table_prefix = $modx->getOption('table_prefix');

$products = $modx->getIterator('msProduct', [
    'class_key' => 'msProduct'
]);
foreach ($products as $product) {
    // Получаем первое изображение
    $sql = "SELECT
            `msProductFile`.`path` AS `path`,
            `msProductFile`.`url` AS `url`
        FROM
            `{$table_prefix}ms2_product_files` AS `msProductFile`
        WHERE
            (
                `msProductFile`.`product_id` = $product->id
                AND `msProductFile`.`parent` = 0
                AND `msProductFile`.`type` = 'image'
            )
        LIMIT 1";
    $result  = $modx->query($sql);
    $row = $result->fetch(PDO::FETCH_ASSOC);

    if (!empty($row['path']) && !empty($row['url'])) {
        // Меняем путь к первой картинке на путь к уменьшенному изображению
        $thumb = str_replace($row['path'], $row['path'] . $thumb_folder, $row['url']);

        $sql = "UPDATE `{$table_prefix}ms2_products` SET thumb = '$thumb' WHERE id = $product->id;";
        $result  = $modx->query($sql);
    }
}
