<?php
$options  = [
    [
        "key" => "cart_module_path",
        'namespace' => "cart",
        "area" => "site",
        "value" => "/core/elements/modules/cart/"
    ]
];

foreach ($options as $option) {
    $setting = $modx->newObject('modSystemSetting');
    $setting->fromArray($option, '', true, true);
    $setting->save();
}
