<?php

/**
 * Скрипт вернет данные с единицами измерений и их ценами по формулам
 * return [{"unit":"шт.","value":55},{"unit":"м3","value":29095}]
 * 
 * В листинге передаем необходимые опции в $fields, так как они не доступы через $resource->get
 */

if (!function_exists('getDefaultUnit')) {
    function getDefaultUnit($fields)
    {
        $output = gettype($fields['unit']) == 'array' ? $fields['unit'][0] : $fields['unit'];
        $replacer = [
            'упаковка' => 'упаковку'
        ];
        if (isset($replacer[$output])) {
            return $replacer[$output];
        } else {
            return $output;
        }
    }
}

require_once MODX_CORE_PATH . "elements/modules/split-unit/snippets/utils.php";

$resource = $modx->resource;

if (empty($fields)) {
    $fields = [
        'unit' => $resource->get('unit'),
        'price' => $resource->get('price'),
        'old_price' => $resource->get('old_price'),
    ];
}

$default = [
    "unit" => getDefaultUnit($fields),
    "price" => $fields['price'],
    "old_price" => $fields['old_price'],
];

$values = [$default];

$formula_path = MODX_CORE_PATH . "elements/modules/split-unit/snippets/formulas/$formula.php";
if (!empty($formula) && file_exists($formula_path)) {
    require_once $formula_path;

    if (function_exists($formula)) {
        $result =  $formula($resource, $fields);
        if (!empty($result))
            $values = array_merge($values, $result);
    }
}

return $values;
