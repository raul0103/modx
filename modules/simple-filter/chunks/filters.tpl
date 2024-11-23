<form class="sf-filters">
  <input type="hidden" name="filter_uniqueid" value="{$filter_uniqueid}" />

  {foreach $filters as $filter} 
    {set $key = $filter['key']}
    <div class="sf-filters__item">
      <div class="sf-filters__item-caption">{$filter['caption']}</div>
      <div class="sf-filters__item-options">
        {foreach $filter['values'] as $index => $value} 
          {if $value}
          <label class="sf-filters__item-option">
            <input
              type="checkbox"
              name="{$key}"
              value="{$value}"
              onchange="window.sf.events.change(this,'{$key}','{$value}')"
            />
            {$value}
          </label>
          {/if} 
        {/foreach}
      </div>
    </div>
  {/foreach}
</form>