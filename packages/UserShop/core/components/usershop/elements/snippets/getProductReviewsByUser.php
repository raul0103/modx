<?php

$user_id = $modx->user->id;

if (!$user_id) return;

$table_prefix = $modx->getOption("table_prefix");

$sql = "SELECT 
            pr.status,
            pr.content,
            sc.uri AS product_uri,
            sc.pagetitle AS product_pagetitle
        FROM {$table_prefix}us_product_reviews AS pr
        LEFT JOIN {$table_prefix}site_content AS sc ON sc.id = pr.product_id
        WHERE pr.user_id = :user_id
        ORDER BY pr.id";

$stmt = $modx->prepare($sql);
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();

$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

return $rows;
