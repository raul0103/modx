<?php

if (!isset($parents)) return false;

$cache_name = md5(serialize($scriptProperties));

if ($save_cache_name) {
    // Плейсхолдер используется для передачи названия в api и получения данных для каталога
    $modx->setPlaceholder($save_cache_name, $cache_name);
}

$cache_options = [
    xPDO::OPT_CACHE_KEY => 'default/map-resources/' . $modx->resource->context_key . '/',
];

if (!$output = $modx->cacheManager->get($cache_name, $cache_options)) {

    $depth = isset($depth) ? $depth : 0;
    $fields = ["id", "pagetitle", "menutitle", "parent", "alias", "uri", "template"]; // Поля получаемые у товаров
    $table_prefix = $modx->getOption('table_prefix');

    // Формируем параметр $where
    if (!empty($where)) {
        $where = json_decode($where, true);

        if (!empty($where)) {
            $where = array_merge([], $where);
        } else {
            $where = [];
        }
    } else {
        $where = [];
    }

    $where['hidemenu'] = 0;
    $where['published'] = 1;

    if (isset($resources)) {
        $where['id:in'] = "($resources)";
    }

    // Переводим where в строку 
    $where_string = "";
    $count = 1;

    foreach ($where as $key => $value) {
        $conditions =  explode(':', $key);

        $field = $conditions[0];
        $operator = isset($conditions[1]) ? $conditions[1] : "=";

        $value = $operator == 'in' ? $value : "'$value'";

        $where_string .= "sc.`$field` $operator $value AND ";
    }

    if ($includeTVs) {
        $includeTVs = "('" . str_replace(',', "','", $includeTVs) . "')";
    }

    /**
     * Запускаем цикл для получения ресурсов
     */
    $fields = 'sc.' . implode(',sc.', $fields);
    $results = [];
    $resources_prev_ids = []; // Родители после каждой итерации, для получения следующей вложенности
    $all_resource_ids = []; // Используется в коде ниже, для получения пропущеных ресурсов
    $tvs = [];
    for ($i = 0; $i <= $depth; $i++) {
        if ($i > 0 && empty($resources_prev_ids)) continue;

        if ($i > 0) {
            $parents = implode(',', $resources_prev_ids);
        }

        if (isset($parents)) {
            $where_parents = "sc.parent IN ($parents)";
        } else {
            $where_parents = "1";
        }

        $sql = "SELECT $fields FROM {$table_prefix}site_content AS sc WHERE $where_string $where_parents";
        $result = $modx->query($sql);
        $rows = $result->fetchAll(PDO::FETCH_ASSOC);

        $resources_prev_ids = array_map(function ($item) {
            return $item['id'];
        }, $rows);
        $all_resource_ids = array_merge($all_resource_ids, $resources_prev_ids);

        $results = array_merge($results, $rows);

        /**
         * Получаем ТВ поля по ID полученых ресурсов 
         * TODO: Возможно лучше вынести из цикла, и получить по всем ID собранных ресурсов
         */
        if ($includeTVs && !empty($resources_prev_ids)) {
            $sql = "SELECT st.name, stc.value, stc.contentid FROM {$table_prefix}site_tmplvars AS st
                LEFT JOIN {$table_prefix}site_tmplvar_contentvalues AS stc
                    ON st.id = stc.tmplvarid
                WHERE stc.contentid IN (" . implode(',', $resources_prev_ids) . ") 
                AND st.name IN ($includeTVs);";

            $result = $modx->query($sql);
            $rows = $result->fetchAll(PDO::FETCH_ASSOC);
            $tvs = array_merge($tvs, $rows);
        }
    }

    /**
     * TV - раскладываем по ключу ресурса 
     */
    $tvs_by_resource = [];
    foreach ($tvs as $tv) {
        if (empty($tvs_by_resource[$tv['contentid']])) {
            $tvs_by_resource[$tv['contentid']] = [];
        }

        $tvs_by_resource[$tv['contentid']][$tv['name']] = $tv['value'];
    }

    /**
     * Подмена ресурсов если передан $transfer_ids
     */
    if ($transfer_ids) {
        $transfer_ids_values = array_unique(array_values($transfer_ids));
        $transfer_ids_keys =  array_keys($transfer_ids);

        /**
         * Проверяем есть ли пропущеные ресурсы
         * Может быть такое что мы получили карту категорий, но в $transfer_ids были переданы товары, поэтому получаем их
         */
        $missed_resources = $difference = array_diff($transfer_ids_keys, $all_resource_ids);
        if (!empty($missed_resources)) {
            $sql = "SELECT $fields FROM {$table_prefix}site_content AS sc WHERE id IN (" . implode(',', $missed_resources) . ")";
            $result = $modx->query($sql);
            $rows = $result->fetchAll(PDO::FETCH_ASSOC);

            $results = array_merge($results, $rows);
        }

        foreach ($results as $index => &$resource) {
            // Если передан $transfer_only - то очищаем родителей переденные в $transfer_ids
            if (
                $transfer_only
                && in_array($resource['parent'], $transfer_ids_values)
                && !in_array($resource['id'], $transfer_ids_keys)
                && $resource['parent'] !== 0
            ) {
                unset($results[$index]);
            }

            $transfer = $transfer_ids[$resource['id']];

            if (isset($transfer)) {
                $resource['parent'] = $transfer;
                $resource['bestseller'] = true;
            }
        }
    }

    /**
     * Сортировка
     */
    if (isset($sortby)) {
        usort($results, function ($a, $b) use ($sortby) {
            // Получаем позиции id в массиве сортировки
            $posA = array_search($a['id'], $sortby);
            $posB = array_search($b['id'], $sortby);

            // Если id есть в массиве сортировки, сортируем по их позиции
            if ($posA !== false && $posB !== false) {
                return $posA - $posB;
            }
            // Если только один из id есть в массиве сортировки, он идет раньше
            if ($posA !== false) return -1;
            if ($posB !== false) return 1;

            // Если ни один из id нет в массиве сортировки, оставляем их порядок
            return 0;
        });
    }



    /**
     * Формируем древовидную структуру
     * Фокус. Работаем с конца, так как будем удалять ресурс который нашел родителя
     */
    $results = array_reverse($results);

    foreach ($results as &$resource) {
        if (isset($tvs_by_resource[$resource['id']])) {
            foreach ($tvs_by_resource[$resource['id']] as $tv_name => $tv_value) {
                $resource[$tv_name] = $tv_value;
            }
        }

        foreach ($results as $index_resource2 => $resource2) {
            if ($resource['id'] == $resource2['parent']) {
                $resource['children'][] = $resource2;
                unset($results[$index_resource2]);
            }
        }
    }

    $output = array_reverse($results);

    $modx->cacheManager->set($cache_name, $output, 0, $cache_options);
}

if (isset($toPlaceholder)) {
    $modx->setPlaceholder($toPlaceholder, $output);
} else {
    return $output;
}
