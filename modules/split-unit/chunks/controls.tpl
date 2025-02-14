<div class="split-unit">
    <span class="split-unit__title">Цена за:</span>
    <div class="split-unit__row">
      {foreach $items as $index => $item}
        <button
          onclick="split_unit.events.activation({$product_id ?: $_modx->resource.id},'{$item['unit']}',this)"
          class="btn btn-bordered {if $index == 0}active{/if}"
        >
          {$item['unit']}
        </button>
      {/foreach}
    </div>
</div>