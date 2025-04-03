<?php

/**
 * Выборка похожих товаров производится по опциям, которые передаются двумя параметрами
 * 
 * filter_options [array] - [ ['key' => 'marka', 'value' => 'valu1'], ['key' => 'standart', 'value' => 'valu2'], ]
 * 
 * 
 */

if (empty($filter_options)) return;

if (!class_exists('getProductsByOptions')) {
    class getProductsByOptions
    {
        static $modx;
        static $table_prefix;
        static $current_product;
        static $filter_options;
        static $limit;

        public function __construct($modx, $filter_options)
        {
            $this->modx = $modx;
            $this->table_prefix = $modx->getOption('table_prefix');
            $this->current_product = [
                'id' => $modx->resource->id,
                'parent' => $modx->resource->parent
            ];
            $this->filter_options = $filter_options;
            $this->limit = 1;
        }

        public function init(): array
        {
            $product_options = $this->findProductsMain();
            if (!$product_options) return [];

            // В удобный массив { "17970":{ "marka":"value1","standart":"value2" } }
            $product_options_by_id = [];
            foreach ($product_options as $product_option) {
                if (!$product_options_by_id[$product_option['product_id']])
                    $product_options_by_id[$product_option['product_id']] = [];
                $product_options_by_id[$product_option['product_id']][$product_option['key']] = $product_option['value'];
            }

            $product_ids = array_keys($product_options_by_id);
            $products = $this->getProducts($product_ids);
            // exit(json_encode($products));
            return $products;
        }

        public function getProducts($product_ids)
        {
            $product_ids = implode(',', $product_ids);
            $sql = "SELECT id,pagetitle,menutitle,alias,uri FROM {$this->table_prefix}site_content WHERE id in ($product_ids) LIMIT {$this->limit}";
            $result = $this->modx->query($sql);

            return $result->fetchAll(PDO::FETCH_ASSOC);
        }

        /**
         * Собираем товары по опциями текущего товара и полученным IDs parents
         */
        public function findProductsMain()
        {
            $where_options = $this->generateWhereOptions();
            $where_product_id = "(SELECT id FROM {$this->table_prefix}site_content WHERE id != {$this->current_product['id']} AND parent = {$this->current_product['parent']})";
            $count_having = count($this->filter_options);

            // Запрос на получение всех опций по полученным товарам
            $sql = "SELECT * FROM {$this->table_prefix}ms2_product_options WHERE product_id IN (SELECT product_id FROM {$this->table_prefix}ms2_product_options AS sc WHERE product_id IN $where_product_id AND $where_options GROUP BY product_id HAVING COUNT(DISTINCT `key`) = $count_having)";
            $result = $this->modx->query($sql);

            return $result->fetchAll(PDO::FETCH_ASSOC);
        }

        public function generateWhereOptions()
        {
            $where_options = [];

            foreach ($this->filter_options as $item) {
                $where_options[] = "`key` = '{$item['key']}' AND `value` = '{$item['value']}'";
            }

            return '(' . implode(') OR (', $where_options) . ')';
        }
    }
}


$getProductsByOptions = new getProductsByOptions($modx, $filter_options);
$result = $getProductsByOptions->init();

return $result;
