<?php

$parent = $modx->getObject('modResource', $modx->resource->parent);
$tv = $parent->getTVValue('similarsample');

if (!$tv) return null;

$modx->addPackage("similarsamples", $modx->getOption("core_path") . "components/similarsamples/model/");

$rules_ids = explode(',', $tv);
$rules = $modx->getCollection('SSRules', ['id:in' => $rules_ids]);

return $rules;
