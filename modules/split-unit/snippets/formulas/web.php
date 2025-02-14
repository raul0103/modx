<?php

function web($resource, $fields)
{
    $output = [];
    $price = parseNum($fields['price']);
    $old_price = parseNum($fields['old_price']);

    $option_value = $fields['ploshad_m2'][0] ?: $resource->get('ploshad_m2')[0];
    if (!empty($option_value)) {
        $output[] = [
            "unit" => "м2",
            "price" => round($price / parseNum($option_value)),
            "old_price" => round($old_price / parseNum($option_value)),
        ];
    }

    $option_value = $fields['obyem_m3'][0] ?: $resource->get('obyem_m3')[0];
    if (!empty($option_value)) {
        $output[] = [
            "unit" => "м3",
            "price" =>  round($price / parseNum($option_value)),
            "old_price" => round($old_price / parseNum($option_value)),
        ];
    }

    return $output;
}
