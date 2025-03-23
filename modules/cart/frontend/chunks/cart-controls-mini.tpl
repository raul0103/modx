{set $product_id = $product_id ?: $_modx->resource.id}
{set $product_data = '@FILE modules/cart/backend/snippets/getProductData.php' | snippet : [
  'product_id' => $product_id
]}

<form
  class="cart-product-mini {if $product_data['count'] > 0}active{/if}"
  data-cart-form="{$product_id}"
  onsubmit="cart.submit(event)"
>
  <input type="hidden" name="id" value="{$product_id}" />
  <input type="hidden" name="price" value="{$price}" />
  <input type="hidden" name="old_price" value="{$old_price}" />
  <input type="hidden" name="unit" value="{$unit | gettype == 'array' ? $unit[0] : $unit}" />

  <div class="w-100">
    <div class="cart-product-mini__row">
      <button class="cart-product-mini__main-btn hide-active btn btn-primary w-100" data-cart-event="plus">
        <svg class="sm-d-block" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"><path d="M5.565 9.557a.75.75 0 1 0-1.49.165l1.49-.165Zm.086 7.563-.746.083.746-.083Zm1.808 1.619v.75-.75Zm9.28 0v-.75.75Zm1.81-1.619.745.083-.745-.083Zm1.575-7.398a.75.75 0 0 0-1.49-.165l1.49.165ZM3 8.89a.75.75 0 0 0 0 1.5v-1.5Zm18.198 1.5a.75.75 0 1 0 0-1.5v1.5ZM5.06 9.304a.75.75 0 1 0 1.342.671l-1.342-.67ZM8.22 6.335a.75.75 0 0 0-1.341-.67l1.341.67Zm9.578 3.64a.75.75 0 0 0 1.342-.67l-1.342.67Zm-.478-4.31a.75.75 0 1 0-1.342.67l1.342-.67Zm-8.7 9.434a.75.75 0 0 0 1.5 0h-1.5Zm1.5-1.82a.75.75 0 1 0-1.5 0h1.5Zm1.23 1.82a.75.75 0 0 0 1.5 0h-1.5Zm1.5-1.82a.75.75 0 0 0-1.5 0h1.5Zm1.229 1.82a.75.75 0 0 0 1.5 0h-1.5Zm1.5-1.82a.75.75 0 0 0-1.5 0h1.5ZM4.074 9.722l.831 7.481 1.491-.166-.83-7.48-1.492.165Zm.831 7.481a2.57 2.57 0 0 0 2.553 2.286v-1.5a1.07 1.07 0 0 1-1.062-.952l-1.49.166ZM7.46 19.49h9.28v-1.5H7.46v1.5Zm9.28 0a2.57 2.57 0 0 0 2.555-2.286l-1.49-.166a1.07 1.07 0 0 1-1.064.952v1.5Zm2.555-2.286.83-7.48-1.49-.166-.83 7.48 1.49.166ZM3 10.39h18.198v-1.5H3v1.5Zm3.4-.415 1.82-3.64-1.341-.67-1.82 3.64 1.342.67Zm12.74-.67-1.82-3.64-1.342.67 1.82 3.64 1.342-.67Zm-9.02 5.794v-1.82h-1.5v1.82h1.5Zm2.73 0v-1.82h-1.5v1.82h1.5Zm2.729 0v-1.82h-1.5v1.82h1.5Z" fill="#fff"/></svg>
        <span class="sm-d-none main-text">В корзину</span>
      </button>

      <a class="cart-product-mini__main-btn show-active nohover btn btn-primary" href="{$_modx->getPlaceholder('makeurls.cart')}">
        <svg class="sm-d-block" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"><path d="M5.565 9.557a.75.75 0 1 0-1.49.165l1.49-.165Zm.086 7.563-.746.083.746-.083Zm1.808 1.619v.75-.75Zm9.28 0v-.75.75Zm1.81-1.619.745.083-.745-.083Zm1.575-7.398a.75.75 0 0 0-1.49-.165l1.49.165ZM3 8.89a.75.75 0 0 0 0 1.5v-1.5Zm18.198 1.5a.75.75 0 1 0 0-1.5v1.5ZM5.06 9.304a.75.75 0 1 0 1.342.671l-1.342-.67ZM8.22 6.335a.75.75 0 0 0-1.341-.67l1.341.67Zm9.578 3.64a.75.75 0 0 0 1.342-.67l-1.342.67Zm-.478-4.31a.75.75 0 1 0-1.342.67l1.342-.67Zm-8.7 9.434a.75.75 0 0 0 1.5 0h-1.5Zm1.5-1.82a.75.75 0 1 0-1.5 0h1.5Zm1.23 1.82a.75.75 0 0 0 1.5 0h-1.5Zm1.5-1.82a.75.75 0 0 0-1.5 0h1.5Zm1.229 1.82a.75.75 0 0 0 1.5 0h-1.5Zm1.5-1.82a.75.75 0 0 0-1.5 0h1.5ZM4.074 9.722l.831 7.481 1.491-.166-.83-7.48-1.492.165Zm.831 7.481a2.57 2.57 0 0 0 2.553 2.286v-1.5a1.07 1.07 0 0 1-1.062-.952l-1.49.166ZM7.46 19.49h9.28v-1.5H7.46v1.5Zm9.28 0a2.57 2.57 0 0 0 2.555-2.286l-1.49-.166a1.07 1.07 0 0 1-1.064.952v1.5Zm2.555-2.286.83-7.48-1.49-.166-.83 7.48 1.49.166ZM3 10.39h18.198v-1.5H3v1.5Zm3.4-.415 1.82-3.64-1.341-.67-1.82 3.64 1.342.67Zm12.74-.67-1.82-3.64-1.342.67 1.82 3.64 1.342-.67Zm-9.02 5.794v-1.82h-1.5v1.82h1.5Zm2.73 0v-1.82h-1.5v1.82h1.5Zm2.729 0v-1.82h-1.5v1.82h1.5Z" fill="#fff"/></svg>
        <span class="sm-d-none main-text">В корзине</span>
        <span class="sm-d-none mini-text">Перейти</span>
      </a>

      <div class="cart-product-mini__controls">
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
    </div>
  </div>
</form>
