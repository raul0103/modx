<?php

if (!$pdoTools = $modx->getService('pdoTools')) {
    $modx->log(xPDO::LOG_LEVEL_ERROR, 'Ошибка загрузки pdoTools');
    return;
}

if (!class_exists('SimilarProductsFinder')) {
    class SimilarProductsFinder
    {
        private $modx;
        private $tablePrefix;
        private $currentProductId;
        private $searchDepth = 3;

        public function __construct(modX &$modx)
        {
            $this->modx = $modx;
            $this->tablePrefix = $modx->getOption('table_prefix');
            $this->currentProductId = $modx->resource->id;
        }

        public function run()
        {
            $rules = $this->getRules();
            $result = [
                'rules' => [],
                'data' => []
            ];

            foreach ($rules as $rule) {
                $options = $this->getProductOptions($rule);
                if (empty($options)) return;

                $parentIds = $this->getAllNestedParentIds($rule);
                $products = $this->findSimilarProducts($options, $parentIds);

                $product_ids = [];
                foreach ($products as $product) {
                    $product_ids[] = (int)$product['product_id'];
                }

                $result['rules'][] = [
                    'name' => $rule->name,
                ];
                $result['data'][] = [
                    'products' => array_values(array_unique($product_ids)),
                    'parents' => $parentIds
                ];
            }

            return $result;
        }

        public function getRules()
        {
            $parent = $this->modx->getObject('modResource', $this->modx->resource->parent);
            $tv = $parent->getTVValue('similarsample');

            if (!$tv) return [];

            $this->modx->addPackage("similarsamples", $this->modx->getOption("core_path") . "components/similarsamples/model/");

            $rules_ids = explode(',', $tv);
            $rules = $this->modx->getCollection('SSRules', ['id:in' => $rules_ids]);
            return $rules;
        }

        private function getProductOptions($rule)
        {
            $options = explode(",", $rule->options);
            $escapedKeys = array_map(function ($key) {
                return $this->modx->quote($key);
            }, $options);
            $keysStr = implode(',', $escapedKeys);

            $sql = "SELECT `key`, `value` FROM {$this->tablePrefix}ms2_product_options 
                    WHERE product_id = {$this->currentProductId} 
                    AND `key` IN ($keysStr)";
            $stmt = $this->modx->query($sql);
            return $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
        }

        private function getAllNestedParentIds($rule)
        {
            $parents = explode(",", $rule->categories);
            $allParentIds = $parents;
            $currentLevel = $parents;

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

$cache = [
    'name' => $modx->resource->id,
    'options' => [
        xPDO::OPT_CACHE_KEY => 'default/similarsamples/' . $modx->resource->context_key . '/',
    ]
];

if (!$result = $modx->cacheManager->get($cache['name'], $cache['options'])) {
    $finder = new SimilarProductsFinder($modx);
    $result = $finder->run();
    $modx->cacheManager->set($cache['name'], $result, 0, $cache['options']);
}

if (empty($result['data'])) return null;

return $pdoTools->getChunk("@FILE _modules/similarsamples/chunks/wrapper.tpl", ["data" => $result]);
