<?php

if (empty($main_options) || empty($parents)) return;

if (!class_exists('SimilarProductsFinder')) {
    class SimilarProductsFinder
    {
        private $modx;
        private $tablePrefix;
        private $currentProductId;
        private $mainOptions;
        private $initialParents;
        private $searchDepth = 3;
        private $cache;

        public function __construct($mainOptions, $parents, modX &$modx)
        {
            $this->modx = $modx;
            $this->tablePrefix = $modx->getOption('table_prefix');
            $this->currentProductId = $modx->resource->id;
            $this->mainOptions = explode(',', $mainOptions);
            $this->initialParents = explode(',', $parents);

            $this->cache = [
                'name' => md5(serialize([$mainOptions, $parents, $this->currentProductId])),
                'options' => [
                    xPDO::OPT_CACHE_KEY => 'default/similarsamples/' . $modx->resource->context_key . '/',
                ]
            ];
        }

        public function run()
        {
            if ($output = $this->modx->cacheManager->get($this->cache['name'], $this->cache['options'])) {
                return $output;
            }

            $options = $this->getProductOptions();
            if (empty($options)) return;

            $parentIds = $this->getAllNestedParentIds();
            $products = $this->findSimilarProducts($options, $parentIds);

            $result = [];
            foreach ($products as $product) {
                $result[] = (int)$product['product_id'];
            }
            $result = [
                'products' => array_values(array_unique($result)),
                'parents' => $parentIds
            ];

            $this->modx->cacheManager->set($this->cache['name'], $result, 0, $this->cache['options']);
            return $result;
        }

        private function getProductOptions()
        {
            $escapedKeys = array_map(function ($key) {
                return $this->modx->quote($key);
            }, $this->mainOptions);
            $keysStr = implode(',', $escapedKeys);

            $sql = "SELECT `key`, `value` FROM {$this->tablePrefix}ms2_product_options 
                    WHERE product_id = {$this->currentProductId} 
                    AND `key` IN ($keysStr)";
            $stmt = $this->modx->query($sql);
            return $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
        }

        private function getAllNestedParentIds()
        {
            $allParentIds = $this->initialParents;
            $currentLevel = $this->initialParents;

            for ($depth = 1; $depth <= $this->searchDepth; $depth++) {
                if (empty($currentLevel)) break;
                $parentsStr = implode(',', array_map('intval', $currentLevel));

                $sql = "SELECT id FROM {$this->tablePrefix}site_content 
                        WHERE class_key = 'msCategory' 
                        AND parent IN ($parentsStr)";
                $stmt = $this->modx->query($sql);
                $rows = $stmt ? $stmt->fetchAll(PDO::FETCH_COLUMN) : [];

                $allParentIds = array_merge($allParentIds, $rows);
                $currentLevel = $rows;
            }

            return array_unique(array_map('intval', $allParentIds));
        }

        private function findSimilarProducts(array $productOptions, array $parentIds)
        {
            $parentIdsStr = implode(',', $parentIds);

            $productSubQuery = "SELECT id FROM {$this->tablePrefix}site_content 
                                WHERE parent IN ($parentIdsStr) 
                                AND id != {$this->currentProductId}";

            $whereOptions = $this->buildOptionWhereClause($productOptions);

            $sql = "SELECT * FROM {$this->tablePrefix}ms2_product_options 
                    WHERE product_id IN (
                        SELECT product_id FROM {$this->tablePrefix}ms2_product_options 
                        WHERE product_id IN ($productSubQuery) 
                        $whereOptions
                        GROUP BY product_id
                    )";

            $stmt = $this->modx->query($sql);
            return $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
        }

        private function buildOptionWhereClause(array $options)
        {
            $clauses = [];
            foreach ($options as $opt) {
                $key = $this->modx->quote($opt['key']);
                $value = $this->modx->quote($opt['value']);
                $clauses[] = "(`key` = $key AND `value` = $value)";
            }
            return empty($clauses) ? '' : 'AND (' . implode(' OR ', $clauses) . ')';
        }
    }
}

$finder = new SimilarProductsFinder($main_options, $parents, $modx);
return $finder->run();
