{* Вынес похожие товары. Используются в нескольких местах через плейсхолдер "similar" *}
{set $data = "@FILE snippets/getJsonData.php" | snippet : [
    "path" => "/assets/template/json/similar-products/"~$_modx->context.key~".json"
]}

{if $data['status'] == 'success'}
    {set $products = "@FILE modules/similar-products/snippets/getSimilarProducts.php" | snippet : [
        'selection_option' => $data['data']['selection_option'],
        'main_options' => $data['data']['main_options'],
        'reserve_options' => $data['data']['reserve_options'],
    ]}
    {$_modx->setPlaceholder("similar", [
        'products' => $products,
        'data' => $data['data']
    ])}
{/if}