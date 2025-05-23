<?php

/** @var modX $modx */

// 1. Создание пространства имён
$namespace = $modx->getObject('modNamespace', ['name' => 'similarsamples']);
if (!$namespace) {
    $namespace = $modx->newObject('modNamespace');
    $namespace->fromArray([
        'name' => 'similarsamples',
        'path' => '{core_path}components/similarsamples/',
        'assets_path' => '{assets_path}components/similarsamples/',
    ], '', true);
    $namespace->save();
    echo "✅ Пространство имён 'similarsamples' создано\n";
} else {
    echo "ℹ️ Пространство имён уже существует\n";
}

// 2. Создание системных настроек
$settings = [
    'similarsamples.core_path' => [
        'value' => '{core_path}components/similarsamples/',
        'xtype' => 'textfield',
        'area' => 'similarsamples',
    ],
    'similarsamples.assets_url' => [
        'value' => '/assets/components/similarsamples/',
        'xtype' => 'textfield',
        'area' => 'similarsamples',
    ],
];

foreach ($settings as $key => $data) {
    if (!$modx->getObject('modSystemSetting', ['key' => $key])) {
        $setting = $modx->newObject('modSystemSetting');
        $setting->fromArray(array_merge([
            'key' => $key,
            'namespace' => 'similarsamples',
        ], $data), '', true);
        $setting->save();
        echo "✅ Системная настройка '{$key}' создана\n";
    } else {
        echo "ℹ️ Системная настройка '{$key}' уже существует\n";
    }
}

// 3. Создание пункта меню
$action = $modx->getObject('modAction', [
    'namespace' => 'similarsamples',
    'controller' => 'home'
]);
if (!$action) {
    $action = $modx->newObject('modAction');
    $action->fromArray([
        'namespace' => 'similarsamples',
        'controller' => 'home',
        'haslayout' => true,
        'lang_topics' => 'similarsamples:default',
    ], '', true);
    $action->save();
    echo "✅ Действие 'home' создано\n";
} else {
    echo "ℹ️ Действие 'home' уже существует\n";
}

// Проверим, есть ли меню
$menu = $modx->getObject('modMenu', ['text' => 'similarsamples']);
if (!$menu) {
    $menu = $modx->newObject('modMenu');
    $menu->fromArray([
        'text' => 'similarsamples',
        'description' => 'Выборки похожих товаров',
        'parent' => 'components',
        'action' => 'home',
        'namespace' => 'similarsamples',
        'menuindex' => 0,
    ], '', true);
    $menu->addOne($action); // Привязываем действие
    $menu->save();
    echo "✅ Меню 'similarsamples' создано\n";
} else {
    echo "ℹ️ Меню 'similarsamples' уже существует\n";
}

// 4. Создаем TV поле
$tv = $modx->getObject('modTemplateVar', ['name' => 'similarsample']);
if (!$tv) {
    $tv = $modx->newObject('modTemplateVar');
    $tv->fromArray([
        'name' => 'similarsample',
        'caption' => 'Выборки товаров',
        'description' => 'SimilarSample',
        'type' => 'listbox-multiple',
        'elements' => '@EVAL $modx->addPackage("similarsamples", $modx->getOption("core_path") . "components/similarsamples/model/"); $resources = $modx->getCollection("SSRules", [ "context_key" => $modx->resource->get("context_key") ]); $results = []; foreach ($resources as $resource) { $results[] = $resource->get("name") . "==" . $resource->get("id"); } return implode("||", $results);',
        'default_text' => '',
        'output_properties' => 'a:1:{s:9:"delimiter";s:1:",";}',
        'rank' => 0,
        'display' => 'default',
    ]);
    if ($tv->save()) {
        echo '✅ TV "similarsample" успешно создано.';
    } else {
        echo 'ℹ️ Ошибка при сохранении TV.';
    }
} else {
    echo 'ℹ️ TV "similarsample" уже существует.';
}
