<?php

/**
 * Выборка похожих товаров производится по опциям, которые передаются двумя параметрами
 * 
 * selection_option [String] - Опция по которой будет составлен финальный массив данных. Пример по длинне: 'item_length'
 * main_options [Array] - Массив опций для выборки товаров: ['collection', 'item_width']
 * reserve_options [Array] - Массив опций по которым будет выборка ЕСЛИ в первом варианте будут дубли
 */

if (!class_exists('similarProducts')) {
    class similarProducts
    {
        static $selection_option;
        static $main_options;
        static $reserve_options;
        static $modx;
        static $table_prefix;
        static $current_product;
        static $settings;
        static $cache;
        static $show_current;

        public function __construct($selection_option, $main_options, $reserve_options, $show_current, $modx)
        {
            $this->selection_option = $selection_option;
            $this->main_options = $main_options;
            $this->reserve_options = $reserve_options;
            $this->show_current = $show_current;
            $this->modx = $modx;
            $this->table_prefix = $modx->getOption('table_prefix');

            $this->current_product = [
                'id' => $modx->resource->id,
                'parent' => $modx->resource->parent
            ];
            $this->settings = [
                'search_product_depth' => 10 // Глубина поиска товаров
            ];

            $this->cache = [
                'name' => $modx->resource->id,
                'options' => [
                    xPDO::OPT_CACHE_KEY => 'default/similar-products/' . $modx->resource->context_key . '/',
                ]
            ];
        }
        public function init()
        {
            if ($output = $this->modx->cacheManager->get($this->cache['name'], $this->cache['options'])) {
                return $output;
            }

            if (!$this->selection_option) return;
            if (!$this->reserve_options) $this->reserve_options = [];

            if ($this->main_options) {
                $product_options = $this->getCurrentProductOptions();
            }

            $parent_ids = $this->getParentsIds();
            $find_products = $this->findProductsMain($product_options, $parent_ids);
            $find_products = $this->deleteDoubles($find_products);

            if (!empty($find_products)) {
                $product_ids = $this->getProductIds($find_products);

                $is_find_reserve = $this->isFindReserve($find_products, $product_ids);
                if ($is_find_reserve && !empty($product_options['reserve'])) {
                    $find_products = $this->findProductsReserve($product_options, $product_ids);
                }
            }

            if (!empty($find_products)) {
                $output = $this->result($find_products);
            } else {
                $output = null;
            }

            $this->modx->cacheManager->set($this->cache['name'], $output, 0, $this->cache['options']);
            return $output;
        }

        /**
         * Удалит дубли товаров по ключевой опции selection_option
         */
        public function deleteDoubles($find_products)
        {
            // 1. Оставили товары по selection_option
            $current_product = null;
            $products = [];
            foreach ($find_products as $find_product) {
                if ($find_product['key'] === $this->selection_option) {
                    $products[] = $find_product;

                    if ($find_product['product_id'] == $this->current_product['id']) {
                        $current_product = $find_product;
                    }
                }
            }

            // 2. Оставили уникальные товары
            $unique = [];
            $result = [];
            foreach ($products as $product) {
                if (!isset($unique[$product['value']])) {
                    $unique[$product['value']] = true;

                    if ($this->show_current && $product['value'] == $current_product['value']) {
                        $result[] = $current_product;
                    } else {
                        $result[] = $product;
                    }
                }
            }




            return $result;
        }

        /**
         * Получить значения опций по текущему товару
         * По этим данным будут найдены похожие товары
         */
        public function getCurrentProductOptions()
        {
            $options_merge = array_merge($this->main_options, $this->reserve_options);
            $options_merge = implode("','", $options_merge);
            $sql = "SELECT * FROM {$this->table_prefix}ms2_product_options AS sc WHERE product_id = {$this->current_product['id']} AND `key` in ('$options_merge')";
            $result = $this->modx->query($sql);
            $rows = $result->fetchAll(PDO::FETCH_ASSOC);

            // Разбили опции текущего товара на части
            $product_options = [
                'main' => [], // Основные опции
                'reserve' => [] // Дополнительные
            ];

            foreach ($rows as $row) {
                if (in_array($row['key'], $this->main_options)) {
                    $product_options['main'][] = $row;
                } else if (in_array($row['key'], $this->reserve_options)) {
                    $product_options['reserve'][] = $row;
                }
            }
            return $product_options;
        }

        /**
         * Получить все дочерние категории по родителю текущего товара
         * Сформируем IDs parents для выборки по ним товаров
         */
        public function getParentsIds()
        {
            $parent_ids = [];
            $resources_prev_ids = []; // Родители после каждой итерации, для получения следующей вложенности
            for ($i = 1; $i <= $this->settings['search_product_depth']; $i++) {
                if ($i > 1 && empty($resources_prev_ids)) continue;
                if ($i > 1) {
                    $parents = implode(',', $resources_prev_ids);
                }

                if (isset($parents)) {
                    $where_parents = "sc.parent IN ($parents)";
                } else {
                    $where_parents = "parent = {$this->current_product['parent']}";
                }

                $sql = "SELECT id FROM {$this->table_prefix}site_content AS sc WHERE class_key = 'msCategory' AND $where_parents";
                $result = $this->modx->query($sql);
                $rows = $result->fetchAll(PDO::FETCH_ASSOC);

                $resources_prev_ids = array_map(function ($item) {
                    return $item['id'];
                }, $rows);

                $parent_ids = array_merge($parent_ids, $rows);
            }
            $parent_ids = array_column($parent_ids, 'id');
            $parent_ids[] = $this->current_product['parent'];

            return $parent_ids;
        }

        /**
         * Собираем товары по опциями текущего товара и полученным IDs parents
         */
        public function findProductsMain($product_options, $parent_ids)
        {
            if (!$this->show_current) {
                $where_not_current_resource = "AND id != {$this->current_product['id']}";
            }

            $parent_ids_implode = implode(',', $parent_ids);
            $where_product_id = "(SELECT id FROM {$this->table_prefix}site_content WHERE parent IN ($parent_ids_implode) $where_not_current_resource)";

            if ($product_options) {
                $where_options = $this->generateWhereOptions($product_options['main']);
                $where_options = "AND ($where_options)";
            }

            // Запрос на получение всех опций по полученным товарам
            $sql = "SELECT * FROM {$this->table_prefix}ms2_product_options WHERE product_id IN (SELECT product_id FROM {$this->table_prefix}ms2_product_options AS sc WHERE product_id IN $where_product_id $where_options GROUP BY product_id);";
            $result = $this->modx->query($sql);

            if ($result === false) {
                return [];
            }

            return $result->fetchAll(PDO::FETCH_ASSOC);
        }

        /**
         * Собираем товары по дополнительным опциям
         */
        public function findProductsReserve($product_options, $product_ids)
        {
            $where_options = $this->generateWhereOptions($product_options['reserve']);
            $where_product_id = implode(',', $product_ids);
            $sql = "SELECT * FROM {$this->table_prefix}ms2_product_options WHERE product_id IN (SELECT product_id FROM {$this->table_prefix}ms2_product_options AS sc WHERE product_id IN ($where_product_id) AND ($where_options) GROUP BY product_id);";
            $result = $this->modx->query($sql);
            return $result->fetchAll(PDO::FETCH_ASSOC);
        }

        /**
         * Проверяем если по опции по которой формируем результат есть дубли, то проводим поиск еще раз но уже с доп. опциями
         * Что-бы оставить больше уникальных 
         */
        public function isFindReserve($find_product_main, $product_ids)
        {
            $selection_options = []; // Значения опций по которым формируется результат
            foreach ($find_product_main as $find_product) {
                if ($find_product['key'] == $this->selection_option) {
                    $selection_options[] = $find_product['value'];
                }
            }
            $selection_options = array_unique($selection_options);

            return count($product_ids) !== count($selection_options);
        }

        /**
         * Получить товары по полученным ID и отдать необходимые поля массивом
         */
        public function result($find_products)
        {
            $product_ids = $this->getProductIds($find_products);
            $product_ids = implode(',', $product_ids);
            $sql = "SELECT id,pagetitle,uri FROM {$this->table_prefix}site_content WHERE id IN ($product_ids)";
            $result = $this->modx->query($sql);
            $rows = $result->fetchAll(PDO::FETCH_ASSOC);

            foreach ($rows as $index => &$row) {
                foreach ($find_products as $find_product) {
                    if ($find_product['product_id'] == $row['id'] && $find_product['key'] == $this->selection_option) {
                        $row['value'] = trim($find_product['value']);
                        break;
                    }
                }

                // Удаляем элемент из массива, если нет значения целевой опции
                if (!$row['value']) {
                    unset($rows[$index]);
                }
            }

            // Сортируем массив по 'value' по возрастанию
            usort($rows, function ($a, $b) {
                return $a['value'] <=> $b['value'];
            });

            return $rows;
        }

        static function generateWhereOptions($items)
        {
            $where_options = [];
            foreach ($items as $item) {
                $where_options[] = "(`key` = '{$item['key']}' AND `value` = '{$item['value']}')";
            }
            return implode(' OR ', $where_options);
        }
        static function getProductIds($find_products)
        {
            $product_ids = array_column($find_products, 'product_id');
            $product_ids = array_unique($product_ids);
            $product_ids = array_values($product_ids);
            return $product_ids;
        }
    }
}

$similarProducts = new similarProducts($selection_option, $main_options, $reserve_options, $show_current, $modx);
$result = $similarProducts->init();

return $result;
