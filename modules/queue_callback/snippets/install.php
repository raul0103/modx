<?php

$packageName = 'queue_callback';
$path = MODX_CORE_PATH . 'elements/modules/' . $packageName . '/model/';

// Генерация классов из схемы
$generator = $modx->getManager()->getGenerator();
$schemaPath = $path . 'schema/' . $packageName . '.mysql.schema.xml';
$generator->parseSchema($schemaPath, $path);

// Создание таблицы
$modx->addPackage('queue_callback', $path);
$manager = $modx->getManager();
$manager->createObjectContainer('QueueCallback');

echo 'Модель сгенерирована';
