{if $product_id}
    {* При AJAX подгрузке товаров не видно данного плейсхолдера. Сработает 1 раз на первом товаре, дальше уже будет подтягивать данные из плейсхолдера *}
    {if !$_modx->getPlaceholder("comparison-products")}
        {'@FILE modules/store-product-selection/snippet/setPlaceholder.php' | snippet : ["cookie_key" => "comparison-products"]}
    {/if}

    {if $product_id in list $_modx->getPlaceholder("comparison-products")} 
        {set $active = "active"}
    {/if}

    <button
    class="comparison-product__btn selection-product-btn btn-icon {$active}"
    onclick="window.addProductSelection(this,{$product_id},'comparison-products', { warning:'Товар удален из сравнения',success:'Товар добавлен в сравнение' })"
    >
        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="35" fill="none"><path clip-rule="evenodd" d="M3.345 27.937v-14.42c-.004-.736.545-1.337 1.226-1.343h4.902c.682.006 1.23.607 1.226 1.343V6.798c-.004-.735.543-1.336 1.224-1.342h4.904c.681.005 1.23.606 1.225 1.342v4.032c-.004-.736.545-1.337 1.226-1.343h4.902c.681.007 1.229.607 1.224 1.343v17.107c.005.736-.544 1.337-1.226 1.342H4.572c-.681-.005-1.23-.606-1.226-1.342Z" stroke="#D3553C" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M9.699 29.28a1 1 0 1 0 2 0h-2Zm2-15.764a1 1 0 0 0-2 0h2ZM17.05 29.28a1 1 0 1 0 2 0h-2Zm2-18.45a1 1 0 0 0-2 0h2Zm-7.352 18.45V13.517h-2v15.762h2Zm7.352 0V10.83h-2v18.45h2Z" fill="#D3553C"/></svg>
        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="35" fill="none"><path clip-rule="evenodd" d="M3.345 27.937v-14.42c-.004-.736.545-1.337 1.226-1.343h4.902c.682.006 1.23.607 1.226 1.343V6.798c-.004-.735.543-1.336 1.224-1.342h4.904c.681.005 1.23.606 1.225 1.342v4.032c-.004-.736.545-1.337 1.226-1.343h4.902c.681.007 1.229.607 1.224 1.343v17.107c.005.736-.544 1.337-1.226 1.342H4.572c-.681-.005-1.23-.606-1.226-1.342Z" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M9.699 29.28a1 1 0 0 0 2 0h-2Zm2-15.764a1 1 0 0 0-2 0h2Zm5.352 15.763a1 1 0 1 0 2 0h-2Zm2-18.45a1 1 0 1 0-2 0h2Zm-7.352 18.45V13.517h-2v15.762h2Zm7.352 0V10.83h-2v18.45h2Z" fill="#fff"/></svg>
    </button>
{else}
    Не передан $product_id
{/if}