<?php

/** @var array $scriptProperties */
/** @var pdoFetch $pdoFetch */
$fqn = $modx->getOption('pdoFetch.class', null, 'pdotools.pdofetch', true);
$path = $modx->getOption('pdofetch_class_path', null, MODX_CORE_PATH . 'components/pdotools/model/', true);
if ($pdoClass = $modx->loadClass($fqn, $path, false, true)) {
  $pdoFetch = new $pdoClass($modx, $scriptProperties);
} else {
  return false;
}
$pdoFetch->addTime('pdoTools loaded');

// Default variables
if (empty($tpl)) {
  $tpl = "@INLINE \n<url>\n\t<loc>[[+url]]</loc>\n\t<lastmod>[[+outdate]]</lastmod>\n\t<changefreq>[[+update]]</changefreq>\n\t<priority>[[+priority]]</priority>\n</url>";
}
if (empty($tplWrapper)) {
  $tplWrapper = "@INLINE <?xml version=\"1.0\" encoding=\"[[++modx_charset]]\"?>\n<urlset xmlns=\"[[+schema]]\">\n[[+output]]\n</urlset>";
}
if (empty($sitemapSchema)) {
  $sitemapSchema = 'http://www.sitemaps.org/schemas/sitemap/0.9';
}
if (empty($outputSeparator)) {
  $outputSeparator = "\n";
}
if (empty($cacheKey)) {
  $scriptProperties['cacheKey'] = 'sitemap/' . substr(md5(json_encode($scriptProperties)), 0, 6);
}

// Convert parameters from GoogleSiteMap if exists
if (!empty($itemTpl)) {
  $tpl = $itemTpl;
}
if (!empty($containerTpl)) {
  $tplWrapper = $containerTpl;
}
if (!empty($allowedtemplates)) {
  $scriptProperties['templates'] = $allowedtemplates;
}
if (!empty($maxDepth)) {
  $scriptProperties['depth'] = $maxDepth;
}
if (isset($hideDeleted)) {
  $scriptProperties['showDeleted'] = !$hideDeleted;
}
if (isset($published)) {
  $scriptProperties['showUnpublished'] = !$published;
}
if (isset($searchable)) {
  $scriptProperties['showUnsearchable'] = !$searchable;
}
if (!empty($googleSchema)) {
  $sitemapSchema = $googleSchema;
}
if (!empty($excludeResources)) {
  $tmp = array_map('trim', explode(',', $excludeResources));
  foreach ($tmp as $v) {
    if (!empty($scriptProperties['resources'])) {
      $scriptProperties['resources'] .= ',-' . $v;
    } else {
      $scriptProperties['resources'] = '-' . $v;
    }
  }
}
if (!empty($excludeChildrenOf)) {
  $tmp = array_map('trim', explode(',', $excludeChildrenOf));
  foreach ($tmp as $v) {
    if (!empty($scriptProperties['parents'])) {
      $scriptProperties['parents'] .= ',-' . $v;
    } else {
      $scriptProperties['parents'] = '-' . $v;
    }
  }
}
if (!empty($startId)) {
  if (!empty($scriptProperties['parents'])) {
    $scriptProperties['parents'] .= ',' . $startId;
  } else {
    $scriptProperties['parents'] = $startId;
  }
}
if (!empty($sortBy)) {
  $scriptProperties['sortby'] = $sortBy;
}
if (!empty($sortDir)) {
  $scriptProperties['sortdir'] = $sortDir;
}
if (!empty($priorityTV)) {
  if (!empty($scriptProperties['includeTVs'])) {
    $scriptProperties['includeTVs'] .= ',' . $priorityTV;
  } else {
    $scriptProperties['includeTVs'] = $priorityTV;
  }
}
if (!empty($itemSeparator)) {
  $outputSeparator = $itemSeparator;
}
//---


$class = 'modResource';
$where = [];
if (empty($showHidden)) {
  $where[] = [
    $class . '.hidemenu' => 0,
    'OR:' . $class . '.class_key:IN' => ['Ticket', 'Article'],
  ];
}
if (empty($context)) {
  $scriptProperties['context'] = $modx->context->key;
}

$select = [$class => 'id,editedon,createdon,context_key,class_key,uri,template'];
if (!empty($useWeblinkUrl)) {
  $select[$class] .= ',content';
}
// Add custom parameters
foreach (['where', 'select'] as $v) {
  if (!empty($scriptProperties[$v])) {
    $tmp = $scriptProperties[$v];
    if (!is_array($tmp)) {
      $tmp = json_decode($tmp, true);
    }
    if (is_array($tmp)) {
      $$v = array_merge($$v, $tmp);
    }
  }
  unset($scriptProperties[$v]);
}
$pdoFetch->addTime('Conditions prepared');

// Default parameters
$default = [
  'class' => $class,
  'where' => json_encode($where),
  'select' => json_encode($select),
  'sortby' => "{$class}.parent ASC, {$class}.menuindex",
  'sortdir' => 'ASC',
  'return' => 'data',
  'scheme' => 'full',
  'limit' => 0,
];
// Merge all properties and run!
$pdoFetch->addTime('Query parameters ready');
$pdoFetch->setConfig(array_merge($default, $scriptProperties), false);

if (!empty($cache)) {
  $data = $pdoFetch->getCache($scriptProperties);
}
if (!isset($return)) {
  $return = 'chunks';
}
if (empty($data)) {
  $now = time();
  $data = $urls = [];
  $rows = $pdoFetch->run();
  foreach ($rows as $row) {
    if (!empty($useWeblinkUrl) && $row['class_key'] == 'modWebLink') {
      $row['url'] = is_numeric(trim($row['content'], '[]~ '))
        ? $pdoFetch->makeUrl((int)trim($row['content'], '[]~ '), $row)
        : $row['content'];
    } else {
      $row['url'] = $pdoFetch->makeUrl($row['id'], $row);

      $row['url'] = str_replace($modx->getOption('http_host'), $_SERVER['HTTP_HOST'], $row['url']);
    }
    unset($row['content']);
    $time = !empty($row['editedon'])
      ? $row['editedon']
      : $row['createdon'];
    $row['date'] = date('c', $time);

    $datediff = floor(($now - $time) / 86400);

    // Приоритет
    if ($row['template'] == 1) {
      $row['priority'] = '0.75';
      $row['update'] = 'daily';
    } elseif ($row['class_key'] == 'modDocument') {
      $row['priority'] = '0.25';
      $row['update'] = 'monthly';
    } elseif ($row['class_key'] == 'msProduct') {
      $row['priority'] = '0.5';
      $row['update'] = 'weekly';
    } elseif ($row['class_key'] == 'msCategory') {
      $row['priority'] = '0.75';
      $row['update'] = 'weekly';
    } else {
      $row['priority'] = '0.5';
      $row['update'] = 'monthly';
    }

    // Рандомный выбор даты
    $daysToSubtract = ($row['id'] % 2 === 0) ? 2 : 3;
    $date = new DateTime('now', new DateTimeZone('UTC'));
    $date->modify("-{$daysToSubtract} days");
    $row['outdate'] = $date->format('Y-m-d\T11:59:28+00:00');

    // Fix possible duplicates made by modWebLink
    if (!empty($urls[$row['url']])) {
      if ($urls[$row['url']] > $row['date']) {
        continue;
      }
    }
    $urls[$row['url']] = $row['date'];

    // Add item to output
    if ($return === 'data') {
      $data[$row['url']] = $row;
    } else {
      $data[$row['url']] = $pdoFetch->parseChunk($tpl, $row);
      if (strpos($data[$row['url']], '[[') !== false) {
        $modx->parser->processElementTags('', $data[$row['url']], true, true, '[[', ']]', array(), 10);
      }
    }
  }
  $pdoFetch->addTime('Rows processed');
  // if (!empty($cache)) {
  //     $pdoFetch->setCache($data, $scriptProperties);
  // }
}

if ($return === 'data') {
  $output = $data;
} else {
  $output = implode($outputSeparator, $data);
  $output = $pdoFetch->getChunk($tplWrapper, [
    'schema' => $sitemapSchema,
    'output' => $output,
    'items' => $output,
  ]);
  $pdoFetch->addTime('Rows wrapped');

  if ($modx->user->hasSessionContext('mgr') && !empty($showLog)) {
    $output .= '<pre class="pdoSitemapLog">' . print_r($pdoFetch->getTime(), 1) . '</pre>';
  }
}
if (!empty($forceXML)) {
  header("Content-Type:text/xml");
  @session_write_close();
  exit($output);
} else {
  return $output;
}
