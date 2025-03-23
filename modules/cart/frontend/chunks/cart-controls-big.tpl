{set $product_id = $product_id ?: $_modx->resource.id}
{set $product_data = '@FILE modules/cart/backend/snippets/getProductData.php' | snippet : [
  'product_id' => $product_id
]}

<form
  class="cart-product-big {if $product_data['count'] > 0}active{/if}"
  data-cart-form="{$product_id}"
  onsubmit="cart.submit(event)"
>
  <input type="hidden" name="id" value="{$product_id}" />
  <input type="hidden" name="price" value="{$price}" />
  <input type="hidden" name="old_price" value="{$old_price}" />
  <input type="hidden" name="unit" value="{$unit | gettype == 'array' ? $unit[0] : $unit}" />

  <div class="w-100">
    <div class="cart-product-big__row">
      <div class="cart-product-big__controls">
        <button class="btn btn-primary" data-cart-event="minus">-</button>
        <input
          class="fw-600"
          type="number"
          value="{$product_data['count'] ?: 1}"
          onchange="cart.submit(event,this.form);"
          data-cart-event="change"
          data-cart-product-count="{$product_id}"
        />
        <button class="btn btn-primary" data-cart-event="plus">+</button>
      </div>
      
      <button class="cart-product-big__main-btn hide-active btn btn-primary w-100" data-cart-event="plus">
        В корзину
      </button>

      <a class="cart-product-big__main-btn show-active nohover btn btn-primary" href="{$_modx->getPlaceholder('makeurls.cart')}">
        В корзине
        <span class="mini-text">Перейти</span>
      </a>
    </div>
  </div>
</form>
