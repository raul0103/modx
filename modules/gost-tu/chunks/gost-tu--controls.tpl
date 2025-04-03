{set $resource_standart = $_modx->resource.standart.0}

{if $resource_standart}

    {if $resource_standart == 'ГОСТ'}
        {set $standart_search = "ТУ"}
    {else}
        {set $standart_search = "ГОСТ"}
    {/if}

    {set $find_products = "@FILE _moduls/gost-tu/snippets/getProductsByOptions.php" | snippet : [
        'filter_options' => [
            ['key' => 'marka', 'value' => $_modx->resource.marka.0],
            ['key' => 'standart', 'value' => $standart_search],
        ]
    ]}

    <div class="d-flex gap-8 mobile-two-rows">
        {foreach ["ГОСТ","ТУ"] as $standart}
            {if $resource_standart == $standart}
                <a class="button fs-12 button-primary">{$standart}</a>
            {else}
                {if count($find_products) > 0}
                    <a class="button fs-12 button-primary--v2" href="{$find_products[0]['uri']}">{$standart}</a>
                {/if}
            {/if}
        {/foreach}
    </div>

{/if}