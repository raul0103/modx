{set $rules = "@FILE modules/similarsamples/snippets/getRules.php" | snippet}

{if $rules}
<div class="similar-samples">

    <div class="similar-samples__tabs">
        {foreach $rules as $index => $rule}
            <div class="similar-samples__tabs-item" data-opened-btn="similar-{$index}" data-toggle-not="true" data-close-early="similar">{$rule->name}</div>
        {/foreach}
    </div>
    
    <div class="similar-samples__products">
        {foreach $rules as $index => $rule}
            {set $data = "@FILE modules/similarsamples/snippets/getSimilarProducts.php" | snippet : [
                'main_options' => $rule->options,
                'parents' => $rule->categories
            ]}
            
            {if $data['products']}
            <div class="similar-samples__products-slider swiper" data-swiper="similar-{$index}"  data-opened-element="similar-{$index}">
                <div class="swiper-wrapper">
                    {'msProducts' | snippet : [
                        'parents' => $data['parents'] | join,
                        'resources' => $data['products'] | join,
                        'tpl' => '@FILE chunks/product/listing-products-item-slide.tpl',
                        'tplWrapper' => '@INLINE {$output}',
                        'includeTVs' => 'isFractional,productNotAvailable,freeShipping',
                        '-includeThumbs' => 'webp',
                    ]}
                </div>
            </div>
            {/if}
        {/foreach}
    </div>
</div>
{/if}
