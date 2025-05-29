<?php

if (!function_exists('getCategories')) {
    function getCategories($products_count, $category_id, $context_key)
    {
        global $modx;
        $table_prefix = $modx->getOption('table_prefix');

        if ($products_count) {
            $sql_having = " HAVING COUNT(c.id) != 0 AND COUNT(c.id) <= $products_count";
        }
        if ($category_id) {
            $sql_category_id = " AND p.id = $category_id";
        }

        $result = $modx->query("SELECT 
                                    p.id AS id,
                                    p.pagetitle AS pagetitle,
                                    COUNT(c.id) AS children_count,
                                    CASE 
                                        WHEN cs.category_id IS NOT NULL THEN TRUE 
                                    ELSE FALSE 
                                        END AS with_rule
                                        
                                FROM {$table_prefix}site_content p
                                LEFT JOIN {$table_prefix}site_content c ON c.parent = p.id AND c.class_key = 'msProduct'
                                LEFT JOIN {$table_prefix}catprod_rules cs ON cs.category_id = p.id
                                WHERE 
                                    p.class_key = 'msCategory'
                                    AND p.context_key = '$context_key'
                                    $sql_category_id
                                GROUP BY p.id
                                $sql_having
                                ORDER BY children_count ASC;");
        $data = $result->fetchAll(PDO::FETCH_ASSOC);
        if (!empty($data)) {
            exit(json_encode([
                'success' => true,
                'data' => $data
            ]));
        } else {
            exit(json_encode([
                'success' => false,
                'data' => "Error fetch categories"
            ]));
        }
    }
}
