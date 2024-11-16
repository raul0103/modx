<?php

$class_key = "msCategory";
$template = 4;
$items = [
    "Колодцы" => "Каталог",
    "Колодезные кольца" => "Колодцы",
];

foreach ($items as $pagetitle => $parent_pagetitle) {
    $doc = $modx->newObject('modDocument');

    $parent = $modx->getObject('modResource', [
        'pagetitle' => $parent_pagetitle
    ]);

    if (!$parent) {
        echo "0 - $pagetitle <br>";
        continue;
    }

    $doc->set('parent', $parent->id);
    $doc->set('pagetitle', $pagetitle);
    $doc->set('template', $template);
    $doc->set('class_key', $class_key);
    $doc->set('published', 1);
    $doc->save();

    echo "{$doc->id} - $pagetitle <br>";
}
