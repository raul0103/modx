<?php
$data = [
    "armatura-v-gatchine.velesark.ru" => 20948884,

];

$settings = $modx->getCollection('modContextSetting');
foreach ($settings as $setting) {
    if ($setting->key === 'http_host') {
        if ($data[$setting->value]) {
            $new_setting = $modx->newObject('modContextSetting');
            $new_setting->fromArray([
                'context_key' => $setting->context_key,
                'key' => 'yandex_id',
                'value' => $data[$setting->value],
                'xtype' => 'textfield',
                'namespace' => 'core',
                'area' => 'core',
            ], '', true);
            $new_setting->save();
        }
    }
}
