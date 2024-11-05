{foreach $items as $index => $item}
{set $pdoid = 'pdopage_'~$index}
{set $pdonav = 'page_'~$index}

<div class="warehouse-table" id="{$pdoid}">
    <div class="warehouse-table__header">
        <h2>{$item['title']} на {$date}</h2>
    </div>

    {set $range_remains = $_modx->resource.range_remains}
    {if $range_remains}
        {$_modx->setPlaceholder('range-remains', $range_remains | split : '-')}
    {/if}

    <div class="rows">
    {'!pdoPage' | snippet :[
        'parents' => $item['parents'],
        'where' => '{"class_key":"msProduct"}',
        'limit' => 10,
        'sortby' => '{"priority1":"ASC", "HitsPage":"ASC"}',
        'includeTVs' => 'priority1,HitsPage',
        'tpl' => '@FILE _modules/warehouses/chunks/product-on-warehouse.tpl',
        'tplWrapper' => '@FILE _modules/warehouses/chunks/wrapper.tpl',
        'ajaxMode' => 'button',
        
        'pageNavVar' => $pdonav,
        'pageVarKey' => $pdonav,

        'ajaxElemWrapper' => '#'~$pdoid,
        'ajaxElemRows' => '#'~$pdoid~' .rows',
        'ajaxElemPagination' => '#'~$pdoid~' .pagination',
        'ajaxElemMore' => '#'~$pdoid~' .btn-more',
        'ajaxElemLink' => '#'~$pdoid~' .pagination a',
    ]}
    </div>
    {$_modx->getPlaceholder($pdonav)}
</div>
{/foreach}