<div id="{$filter_uniqueid}" class="mf-products">
    <button onclick="window.sf.events.reset('{$filter_uniqueid}')">Сбросить</button>
    <div class="mf-products__pagination" id="pagen-{$filter_uniqueid}" data-sf-total-pages="{$total_pages}"></div>
    
    <div class="mf-products__row">
        {foreach $products as $product}
        <div class="mf-products__item">
            <a class="mf-products__item-title" href="{$product['uri']}">{$product['pagetitle']}</a>
            <div class="mf-products__item-price">Цена {$product['price']} руб.</div>
            <ul>
                {foreach $options as $option}
                    <li>{$option}: {$product[$option]}</li>
                {/foreach}
            </ul>
        </div>
        {/foreach}
    </div>
</div>