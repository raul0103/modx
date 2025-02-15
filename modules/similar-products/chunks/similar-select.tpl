{set $similar = $_modx->getPlaceholder("similar")}

{if $similar}
<div class="product-page__inform-section d-grid gap-5">
  {$similar['data']['title']}
  
  <div class="custom-select">
    <button data-custom-select-btn="similar-products" type="button">{$_modx->resource[$similar['data']['selection_option']][0]} {$similar['data']['unit']}</button>

    <div
      data-custom-select-element="similar-products"
      class="custom-select__dropdown hidden"
    >
      <div class="custom-select__dropdown-group">
        {foreach $similar['products'] as $similar_product}
          <a class="custom-select__dropdown-item" href="{$similar_product['uri']}" data-custom-select-item="">
            {$similar_product['value']} {$similar['data']['unit']}
          </a>
        {/foreach}
      </div>
    </div>
  </div>
</div>
{/if}