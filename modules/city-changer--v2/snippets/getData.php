<?php

$path_to_data = MODX_CORE_PATH . "/elements/modules/city-changer/json/data.json";

if (!file_exists($path_to_data)) return;

$data = file_get_contents($path_to_data);
$data = json_decode($path_to_data, true);

return $data;
