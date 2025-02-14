{foreach $items as $index => $item}
  <span
    data-split-unit-product="{$product_id ?: $_modx->resource.id}"
    data-split-unit="{$item['unit']}"
    class="split-unit-price fs-30 {if $index == 0}opened{/if}"
  >
    {$item['price']} ₽
  </span>

  {if $item['old_price']}
    <span
      data-split-unit-product="{$product_id ?: $_modx->resource.id}"
      data-split-unit="{$item['unit']}"
      class="old-price color-gray fw-700 {if $index == 0}opened{/if}"
    >
      {$item['old_price']} ₽
    </span>
  {/if} 
{/foreach}
