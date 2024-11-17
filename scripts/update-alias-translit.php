<?php

$resrs = $modx->getCollection('modResource', [
    'class_key' => 'msCategory'
]);

foreach ($resrs as $res) {
    $alias = modResource::filterPathSegment($modx, $res->pagetitle);
    $res->set('alias', $alias);

    $res->save();
}
