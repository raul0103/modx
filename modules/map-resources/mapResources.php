<?php

if (empty($parents)) return false;

$cache_name = md5(serialize($scriptProperties));
$cache_options = [
    xPDO::OPT_CACHE_KEY => 'default/map-resources/' . $modx->resource->context_key . '/',
];

if (!$output = $modx->cacheManager->get($cache_name, $cache_options)) {

    $depth = isset($depth) ? $depth : 0;
    $fields = ["id", "pagetitle", "menutitle", "parent", "alias", "uri"]; // Поля получаемые у товаров
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
    $tvs = [];
    for ($i = 0; $i <= $depth; $i++) {
        if ($i > 0 && empty($resources_prev_ids)) continue;

        if ($i > 0) {
            $parents = implode(',', $resources_prev_ids);
        }

        if (!empty($parents)) {
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
