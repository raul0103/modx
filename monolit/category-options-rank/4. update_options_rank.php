<?php

$category_options = json_decode('{
  "konkovo-karniznaya-cherepica-tegola": {
    "upakovka": 0,
    "unit": 1,
    "ottenok": 2,
    "kolvom2upak": 3,
    "item_width": 4,
    "item_thickness": 5,
    "item_length": 6,
    "dlina-m": 7,
    "shirina-m": 8,
    "v_upakovke": 9,
    "ves-upakovki": 10,
    "kolichestvo-v-upakovke-m-p": 11,
    "ves-plity-kg": 12,
    "dlina-rulona": 13,
    "shirina-rulona": 14
  },
  "konkovo-karniznaya-cherepica-tehnonikol-shinglas": {
    "obyem_m3": 0,
    "v_upakovke": 1,
    "item_width": 2,
    "item_thickness": 3,
    "item_length": 4,
    "massa": 5,
    "unit": 6,
    "grandlineid": 7,
    "ottenok": 8,
    "pokrytie": 9,
    "collection": 10,
    "proizvoditel": 11,
    "strana": 12,
    "forma-narezki": 13,
    "material": 14,
    "garantiya": 15,
    "teplostojkost": 16,
    "kolvom2upak": 17,
    "kolichestvo-sloev": 18,
    "seria": 19,
    "kolichestvo-v-upakovke-m-p": 20
  }
}', true);

const CATEGORY_TEMPLATE_ID = 2;

function fetchData($sql, $field_key, $field_value)
{
  global $modx;

  $stmt = $modx->query($sql);
  $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

  $data = [];
  foreach ($rows as $row) {
    $data[$row[$field_key]] = $row[$field_value];
  }

  return $data;
}

$options = fetchData(
  "SELECT id, `key` FROM modx_ms2_options",
  "key",
  "id"
);

$categories = fetchData(
  "SELECT id, alias FROM modx_site_content WHERE class_key = 'msCategory' AND template = " . CATEGORY_TEMPLATE_ID,
  "alias",
  "id"
);

$updateStmt = $modx->prepare("UPDATE modx_ms2_category_options SET `rank` = :rank WHERE option_id = :option_id AND category_id = :category_id");

foreach ($categories as $alias => $category_id) {

  if (!isset($category_options[$alias])) {
    continue;
  }

  foreach ($category_options[$alias] as $option_key => $rank) {

    if (!isset($options[$option_key])) continue;

    $option_id = $options[$option_key];

    $updateStmt->execute([
      'rank'        => $rank,
      'option_id'   => $option_id,
      'category_id' => $category_id,
    ]);
  }

  echo $category_id . ' success ' . PHP_EOL;
}
