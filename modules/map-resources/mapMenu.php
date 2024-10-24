<?php

if (empty($data)) return;

$properties = [
    'hereClass' => $hereClass ?: 'active',
    'tpl' => $tpl ? $tpl : '<li class="{$classnames}"><a href="{$uri}">{$menutitle}</a></li>',
    'tplOuter' =>  $tplOuter ? $tplOuter : '<ul>{$wrapper}</ul>',
    'tplParentRow' =>  $tplParentRow ? $tplParentRow : '<ul class="submenu">{$wrapper}</ul>',
    'tplInnerRow' => $tplInnerRow ? $tplInnerRow : '<li class="{$classnames}"><a href="{$uri}">{$menutitle}</a>{$wrapper}</li>',
];

if (!function_exists('mapClassNames')) {

    function mapClassNames($properties, $item)
    {
        $classnames = '';
        $classnames .= ('/' . $item['uri'] === $_SERVER['REQUEST_URI']) ? $properties['hereClass'] : '';
        return $classnames;
    }
}

if (!function_exists('mapParser')) {
    // Функция для замены ключей на значения из массива
    function mapParser($template, $data)
    {
        // Используем регулярное выражение для поиска {$ключей}
        return preg_replace_callback('/\{\$(.*?)\}/', function ($matches) use ($data) {
            $key = $matches[1]; // Получаем ключ из {$ключа}
            return isset($data[$key]) ? $data[$key] : $matches[0]; // Заменяем на значение из массива или оставляем как есть
        }, $template);
    }
}

if (!function_exists('mapMenu')) {
    function mapMenu($properties, $items, $is_submenu = false)
    {
        $output = '';
        $idx = 1;
        foreach ($items as $item) {
            $item['classnames'] = mapClassNames($properties, $item);
            $item['idx'] = $idx++;

            if (!empty($item['children'])) {
                $children_html = mapMenu($properties, $item['children'], true);
                $item['wrapper'] = $children_html;

                $output .= mapParser($properties['tplInnerRow'], $item);
            } else {
                $output .= mapParser($properties['tpl'], $item);
            }
        }

        $tpl_parent = $is_submenu ? $properties['tplParentRow'] : $properties['tplOuter'];
        return mapParser($tpl_parent, [
            'wrapper' => $output
        ]);
    }
}

$cache_name = md5(serialize($scriptProperties));
$cache_options = [
    xPDO::OPT_CACHE_KEY => 'default/map-resources/' . $modx->resource->context_key . '/',
];

if (!$output = $modx->cacheManager->get($cache_name, $cache_options)) {
    $output = mapMenu($properties, $data);
    $modx->cacheManager->set($cache_name, $output, 0, $cache_options);
}
return $output;
