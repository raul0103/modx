<?php

$table_prefix = $modx->getOption("table_prefix");
$product_id = $modx->resource->id;

$response = $modx->query("SELECT * FROM {$table_prefix}us_product_reviews
                                WHERE
                                    product_id = $product_id
                                    AND status = 'approved'
                                    ORDER BY id");
$rows = $response->fetchAll(PDO::FETCH_ASSOC);

$result = [
    'reviews_count' => count($rows),
    'average_rating' => 0
];
if (count($rows) > 0) {
    foreach ($rows as $row) {
        $result['average_rating'] += $row['rating'];
    }

    $result['average_rating'] = round($result['average_rating'] / count($rows), 1);
}

return $result;
