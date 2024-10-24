<?php

/**
 * 1. Прописать условия для получения товаров
 * 2. При падении по таймауту просто перезапустить скрипт, логи не позволят повторно обходить пройденные ресурсы
 */

$ctx = 'krovlya';

$filelog = MODX_BASE_PATH . "1cache-clear-log.log";
if (file_exists($filelog)) {
    $passed_ids = file_get_contents($filelog);
    $passed_ids = explode(',', $passed_ids);
} else {
    $passed_ids = [0]; // С пустым массиво where не найдет ресурсы
}

$resrs = $modx->getIterator('modResource', [
    'class_key' => 'msProduct',
    'context_key' => $ctx,
    'id:NOT IN' => $passed_ids
]);

foreach ($resrs as $res) {
    file_put_contents($filelog, $res->id . ',', FILE_APPEND);

    $ck = $res->getCacheKey();

    $mgrCtx = $modx->context->get('key');
    $cKey = str_replace($mgrCtx, $ctx, $ck);

    $modx->cacheManager->delete(
        $cKey,
        array(
            xPDO::OPT_CACHE_KEY => $modx->getOption('cache_resource_key', null, 'resource'),
            xPDO::OPT_CACHE_HANDLER => $modx->getOption(
                'cache_resource_handler',
                null,
                $modx->getOption(xPDO::OPT_CACHE_HANDLER)
            ),
            xPDO::OPT_CACHE_FORMAT => (int)$modx->getOption(
                'cache_resource_format',
                null,
                $modx->getOption(xPDO::OPT_CACHE_FORMAT, null, xPDOCacheManager::CACHE_PHP)
            )
        )
    );
}
