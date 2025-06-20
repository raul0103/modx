<?php

$table_prefix = $modx->getOption("table_prefix");
$product_id = (int) $modx->resource->get('id');

$sql = "SELECT * 
        FROM {$table_prefix}us_product_reviews
        WHERE product_id = :product_id
          AND status = 'approved'
        ORDER BY id";

$stmt = $modx->prepare($sql);
$stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
$stmt->execute();

$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

return $rows;
