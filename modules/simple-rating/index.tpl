{if !$rating} {set $rating = 5} {/if}

<div class="rating {if $clicable}clicable{/if}">
  {foreach range(1,5) as $index => $star}
  <span class="star {if $rating >= $index + 1}active{/if}" {if $clicable}data-rating-star{/if}>★</span>
  {/foreach}
</div>
