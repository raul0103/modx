<?php

$packageName = 'usershop';
$path = MODX_CORE_PATH . 'components/' . $packageName . '/model/';

// Генерация классов из схемы
$generator = $modx->getManager()->getGenerator();
$schemaPath = $path . 'schema/' . $packageName . '.mysql.schema.xml';

// Проверьте, что схема существует
if (file_exists($schemaPath)) {
    $generator->parseSchema($schemaPath, $path);
} else {
    echo "Схема не найдена: $schemaPath";
}

// Создание таблицы
$modx->addPackage('usershop', $path);
$manager = $modx->getManager();

$manager->createObjectContainer('OrderReviews');
$manager->createObjectContainer('UserDiscount');
$manager->createObjectContainer('UserTickets');
$manager->createObjectContainer('UserProductReviews');

echo 'Модель сгенерирована';
