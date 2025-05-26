<div class="similar-samples">
    <div class="similar-samples__container">
        <h2 class="similar-samples__title section__title">Вам могут понадобиться</h2>

        <div class="similar-samples__tabs">
            {set $index = 0}
            {foreach $data['rules'] as $rule}
                {set $index = $index + 1}
                <div class="similar-samples__tabs-item {if $index == 1}active{/if}" data-opened-btn="similar-{$index}" data-toggle-not="true" data-close-early="similar">{$rule['name']}</div>
            {/foreach}
        </div>
        
        <div class="similar-samples__products">
            {set $index = 0}
            {foreach $data['data'] as $value}
                {set $index = $index + 1}
                <article class="other-products similar-samples__products-slider {if $index == 1}opened{/if}" id="other-products" data-opened-element="similar-{$index}">
                    <div class="other-products__container">
                      <div class="other-products__slider">
                        <div class="swiper-container swiper-container-initialized swiper-container-horizontal swiper-container-pointer-events">
                          <div class="swiper-wrapper" style="transform: translate3d(0px, 0px, 0px); transition-duration: 0ms;">
                            {'msProducts' | snippet : [
                                'parents' => $value['parents'] | join,
                                'resources' => $value['products'] | join,
                                'tpl' => '@FILE chunks/product/listing-products-item-slide.tpl',
                                'tplWrapper' => '@INLINE {$output}',
                                'includeTVs' => 'isFractional,productNotAvailable,freeShipping',
                                '-includeThumbs' => 'webp',
                            ]}
                          </div>
                        </div>
                        <div class="swiper-buttons">
                          <div class="swiper-button swiper-button-prev swiper-button-disabled"></div>
                          <div class="swiper-button swiper-button-next swiper-button-disabled"></div>
                        </div>
                      </div>
                    </div>
                </article>
            {/foreach}
        </div>
    </div>
</div>