<?php
$table_prefix = $modx->getOption("table_prefix");
$product_id = (int) $modx->resource->get('id');

$sql = "SELECT rating
        FROM {$table_prefix}us_product_reviews
        WHERE product_id = :product_id
          AND status = 'approved'
        ORDER BY id";

$stmt = $modx->prepare($sql);
$stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
$stmt->execute();

$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

$result = [
    'reviews_count'    => count($rows),
    'average_rating'   => 0.0,
];

// Подсчёт среднего
if ($result['reviews_count'] > 0) {
    $sum = 0;
    foreach ($rows as $row) {
        $sum += floatval($row['rating']);
    }
    $result['average_rating'] = round($sum / $result['reviews_count'], 1);
}

return $result;
