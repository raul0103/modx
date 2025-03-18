<?php

// $items = $modx->getCollection('modContext');

// foreach($items as $item){
//     echo $item->key.PHP_EOL;
// }

$contexts = [
    'voronezh' => ['source_id' => 703],
];

foreach ($contexts as $ctxKey => $ctxValues) {
    foreach ($ctxValues as $stgKey => $stgVal) {
        $stg = $modx->getObject('modContextSetting', [
            'context_key' => $ctxKey,
            'key' => $stgKey
        ]);

        handleStg($stg, $ctxKey, $stgKey, $stgVal);
    }
}

function handleStg($stg, $ctxKey, $stgKey, $stgVal)
{
    global $modx;

    if (empty($stg)) {
        $resultCreate = $modx->runProcessor('context/setting/create', [
            'fk' => $ctxKey,
            'key' => $stgKey,
            'name' => $stgKey,
            'xtype' => 'textfield',
            'namespace' => 'core',
            'value' => $stgVal,
        ]);

        if ($resultCreate->response['success']) {
            echo "Успешно создана настройка $stgKey для контекста $ctxKey<br>";
        } else {
            echo "Не удалось создать настройку $stgKey для контекста $ctxKey. message: {$resultCreate->response['message']}<br>";
        }

        // $modx->error = new modError($modx);
    } else {
        $stg->value = $stgVal;
        $resultSave = $stg->save();

        if ($resultSave) {
            echo "Успешно обновлена настройка $stgKey для контекста $ctxKey<br>";
        } else {
            echo "Не удалось обновить настройку $stgKey для контекста $ctxKey<br>. message: {$resultSave->response['message']}<br>";
        }
    }
}

echo 'Конец работы скрипта';
