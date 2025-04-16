<?php

/** @var modX $modx */

// 1. Создание пространства имён
$namespace = $modx->getObject('modNamespace', ['name' => 'usershop']);
if (!$namespace) {
    $namespace = $modx->newObject('modNamespace');
    $namespace->fromArray([
        'name' => 'usershop',
        'path' => '{core_path}components/usershop/',
        'assets_path' => '{assets_path}components/usershop/',
    ], '', true);
    $namespace->save();
    echo "✅ Пространство имён 'usershop' создано\n";
} else {
    echo "ℹ️ Пространство имён уже существует\n";
}

// 2. Создание системных настроек
$settings = [
    'usershop.core_path' => [
        'value' => '{core_path}components/usershop/',
        'xtype' => 'textfield',
        'area' => 'usershop',
    ],
    'usershop.assets_url' => [
        'value' => '/assets/components/usershop/',
        'xtype' => 'textfield',
        'area' => 'usershop',
    ],
];

foreach ($settings as $key => $data) {
    if (!$modx->getObject('modSystemSetting', ['key' => $key])) {
        $setting = $modx->newObject('modSystemSetting');
        $setting->fromArray(array_merge([
            'key' => $key,
            'namespace' => 'usershop',
        ], $data), '', true);
        $setting->save();
        echo "✅ Системная настройка '{$key}' создана\n";
    } else {
        echo "ℹ️ Системная настройка '{$key}' уже существует\n";
    }
}

// 3. Создание пункта меню
$action = $modx->getObject('modAction', [
    'namespace' => 'usershop',
    'controller' => 'home'
]);
if (!$action) {
    $action = $modx->newObject('modAction');
    $action->fromArray([
        'namespace' => 'usershop',
        'controller' => 'home',
        'haslayout' => true,
        'lang_topics' => 'usershop:default',
    ], '', true);
    $action->save();
    echo "✅ Действие 'home' создано\n";
} else {
    echo "ℹ️ Действие 'home' уже существует\n";
}

// Проверим, есть ли меню
$menu = $modx->getObject('modMenu', ['text' => 'usershop']);
if (!$menu) {
    $menu = $modx->newObject('modMenu');
    $menu->fromArray([
        'text' => 'usershop',
        'description' => 'Пользователи магазина',
        'parent' => 'components',
        'action' => 'home',
        'namespace' => 'usershop',
        'menuindex' => 0,
    ], '', true);
    $menu->addOne($action); // Привязываем действие
    $menu->save();
    echo "✅ Меню 'usershop' создано\n";
} else {
    echo "ℹ️ Меню 'usershop' уже существует\n";
}
