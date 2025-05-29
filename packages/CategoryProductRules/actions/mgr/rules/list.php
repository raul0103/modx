<?php

if (!function_exists('getRules')) {
    function getRules($context_key)
    {
        global $modx;
        $table_prefix = $modx->getOption('table_prefix');

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
                                    p.id IN (SELECT category_id FROM {$table_prefix}catprod_rules WHERE context_key = '$context_key') 
                                GROUP BY p.id
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
                'data' => "Error fetch rules"
            ]));
        }
    }
}
