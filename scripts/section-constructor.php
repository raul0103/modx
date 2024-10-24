<?php

/**
 * По TV полю ресурса [section-constructor] - получает список путей к файлам (блокам)
 */

if (!$pdoTools = $modx->getService('pdoTools')) return;

$sections = $modx->resource->getTVValue("section_constructor");
if (empty($sections)) return;

$sections = explode(',', $sections);

foreach ($sections as $index => $section) {
    $path = MODX_CORE_PATH . 'elements/' . $section;
    if (!file_exists($path)) continue;

    echo $pdoTools->getChunk("@FILE $section");
}
