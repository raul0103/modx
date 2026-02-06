<?php
$modx_ms2_category_options = [
	[
		'alias' => 'konkovo-karniznaya-cherepica-tegola',
		'key' => 'upakovka',
		'rank' => 0,
	],[
		'alias' => 'konkovo-karniznaya-cherepica-tegola',
		'key' => 'kolvom2upak',
		'rank' => 3,
	],[
		'alias' => 'konkovo-karniznaya-cherepica-tegola',
		'key' => 'ottenok',
		'rank' => 2,
	],	[
		'alias' => 'konkovo-karniznaya-cherepica-tegola',
		'key' => 'unit',
		'rank' => 1,
	],[
		'alias' => 'konkovo-karniznaya-cherepica-tehnonikol-shinglas',
		'key' => 'massa',
		'rank' => 5,
	],[
		'alias' => 'konkovo-karniznaya-cherepica-tehnonikol-shinglas',
		'key' => 'item_length',
		'rank' => 4,
	],[
		'alias' => 'konkovo-karniznaya-cherepica-tehnonikol-shinglas',
		'key' => 'item_thickness',
		'rank' => 3,
	],[
		'alias' => 'konkovo-karniznaya-cherepica-tehnonikol-shinglas',
		'key' => 'item_width',
		'rank' => 2,
	],	[
		'alias' => 'konkovo-karniznaya-cherepica-tehnonikol-shinglas',
		'key' => 'v_upakovke',
		'rank' => 1,
	],
	[
		'alias' => 'konkovo-karniznaya-cherepica-tehnonikol-shinglas',
		'key' => 'obyem_m3',
		'rank' => 0,
	],
];

$result = [];
foreach($modx_ms2_category_options as $modx_ms2_category_option){
	if(!$result[$modx_ms2_category_option['alias']]){
		$result[$modx_ms2_category_option['alias']] = [];
	}

	$result[$modx_ms2_category_option['alias']][$modx_ms2_category_option['key']] = $modx_ms2_category_option['rank'];
}

$path = 'C:\OSPanel\home\stroymarket-2__osnova\_DEV\category-options-rank\output.json';
file_put_contents($path, json_encode($result));
