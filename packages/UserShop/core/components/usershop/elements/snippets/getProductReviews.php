<?php

$table_prefix = $modx->getOption("table_prefix");
$product_id = $modx->resource->id;

$response = $modx->query("SELECT * FROM {$table_prefix}us_product_reviews
                                WHERE
                                    product_id = $product_id
                                    AND status = 'approved'
                                    ORDER BY id");
$rows = $response->fetchAll(PDO::FETCH_ASSOC);

return $rows;
