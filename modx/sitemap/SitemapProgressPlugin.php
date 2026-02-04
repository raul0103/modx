<?php

// SitemapProgressPlugin
// Плагин разбивает sitemap на несколько частей по class_key
// Установить событие - OnLoadWebDocument

// -------------------------------------------------
// Конфигурация
// -------------------------------------------------

const PRODUCT_LIMIT = 5000; // Лимит товаров для создания пагинации
// Адреса на сайтмапы
const SITEMAP_LINKS = [
  'products' => 'products/sitemap.xml', // Ресурсы class_key = msProducts
  'categories' => 'categories/sitemap.xml', // Ресурсы class_key = msCategories
  'resources' => 'resources/sitemap.xml', // Ресурсы class_key = modResource
  'base' => 'sitemap.xml', // Основной. Проверяем в последнюю очередь так как вхождение есть в любой из ссылок
];

// -------------------------------------------------
// Необходимые функции и классы
// -------------------------------------------------

if (!class_exists('smProducts')) {
  class smProducts
  {
    /**
     * Кол-во товаров
     * @return int
     */
    public static function getCount()
    {
      global $modx;
      $stmt = $modx->prepare("SELECT count(id) as count FROM modx_site_content WHERE class_key = :class_key AND context_key = :context_key");

      $stmt->bindValue(':context_key', $modx->context->key, PDO::PARAM_STR);
      $stmt->bindValue(':class_key', 'msProduct', PDO::PARAM_STR);
      $stmt->execute();

      return (int)$stmt->fetch(PDO::FETCH_COLUMN);
    }

    /**
     * Отдает все товары
     * @return array
     */
    public static function getAll()
    {
      global $modx;
      $stmt = $modx->prepare("SELECT id, uri FROM modx_site_content WHERE class_key = :class_key AND context_key = :context_key AND `deleted` = :deleted AND `published` = :published");

      $stmt->bindValue(':context_key', $modx->context->key, PDO::PARAM_STR);
      $stmt->bindValue(':class_key', 'msProduct', PDO::PARAM_STR);
      $stmt->bindValue(':deleted', 0, PDO::PARAM_INT);
      $stmt->bindValue(':published', 1, PDO::PARAM_INT);
      $stmt->execute();
      $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

      return $products;
    }

    /**
     * Отдает товары по пагинации
     * @param mixed $offset
     * @return array
     */
    public static function getByOffset($limit, $offset = 0)
    {
      global $modx;

      $stmt = $modx->prepare("SELECT id, uri FROM modx_site_content WHERE class_key = :class_key AND context_key = :context_key AND `deleted` = :deleted AND `published` = :published LIMIT :limit OFFSET :offset");

      $stmt->bindValue(':context_key', $modx->context->key, PDO::PARAM_STR);
      $stmt->bindValue(':class_key', 'msProduct', PDO::PARAM_STR);
      $stmt->bindValue(':deleted', 0);
      $stmt->bindValue(':published', 1);

      $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
      $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
      $stmt->execute();
      $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

      return $products;
    }
  }
}

if (!class_exists('smCategories')) {
  class smCategories
  {
    /**
     * Получить все категории
     * @return array
     */
    public static function getAll()
    {
      global $modx;

      $stmt = $modx->prepare("SELECT id, uri FROM modx_site_content WHERE class_key = :class_key AND context_key = :context_key AND `deleted` = :deleted AND `published` = :published");

      $stmt->bindValue(':context_key', $modx->context->key, PDO::PARAM_STR);
      $stmt->bindValue(':class_key', 'msCategory', PDO::PARAM_STR);
      $stmt->bindValue(':deleted', 0, PDO::PARAM_INT);
      $stmt->bindValue(':published', 1, PDO::PARAM_INT);
      $stmt->execute();

      return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
  }
}

if (!class_exists('smResources')) {
  class smResources
  {
    /**
     * Получить все ресурсы
     * @return array
     */
    public static function getAll()
    {
      global $modx;

      $stmt = $modx->prepare("SELECT id, uri FROM modx_site_content WHERE class_key = :class_key AND context_key = :context_key AND `deleted` = :deleted AND `published` = :published AND template != :template");

      $stmt->bindValue(':context_key', $modx->context->key, PDO::PARAM_STR);
      $stmt->bindValue(':class_key', 'modDocument', PDO::PARAM_STR);
      $stmt->bindValue(':template', 0, PDO::PARAM_INT);
      $stmt->bindValue(':deleted', 0, PDO::PARAM_INT);
      $stmt->bindValue(':published', 1, PDO::PARAM_INT);
      $stmt->execute();

      return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
  }
}

if (!function_exists('smRandomDate')) {
  /**
   * Summary of smRandomDate
   * @param mixed $coefficient - По какому коэффициенту подбираем рандомноую дату, например по ID ресурса или индекс массива
   * @return string
   */
  function smRandomDate(int $coefficient = 1)
  {
    $tz = new DateTimeZone('UTC');
    $now = new DateTime('now', $tz);

    // 1. Неделя месяца
    $dayOfMonth = (int)$now->format('j');
    $firstDayOfMonth = new DateTime($now->format('Y-m-01'), $tz);
    $firstWeekday = (int)$firstDayOfMonth->format('N');
    $weekOfMonth = (int)ceil(($dayOfMonth + $firstWeekday - 1) / 7);

    // 2. Seed (детерминированный)
    $seedSource = $now->format('Y-m') . '-' . $weekOfMonth . '-' . $coefficient;
    mt_srand(crc32($seedSource));

    // 3. Смещение от 1 до 6 дней
    $daysToSubtract = mt_rand(1, 6);

    // 4. Формируем дату
    $date = new DateTime('now', $tz);
    $date->modify("-{$daysToSubtract} days");

    // 5. Рандомное время
    $date->setTime(
      mt_rand(0, 23),
      mt_rand(0, 59),
      mt_rand(0, 59)
    );

    return $date->format('Y-m-d\TH:i:s+00:00');
  }
}

if (!function_exists('smPageOutput')) {
  /**
   * Отдает данные на страницу
   */
  function smPageOutput($content)
  {
    header('HTTP/1.1 200 OK');
    header('Content-Type: application/xml; charset=UTF-8');

    exit($content);
  }
}



// -------------------------------------------------
// Опеределяем нужный сайтмап
// -------------------------------------------------
$extension = pathinfo($_SERVER['REQUEST_URI'])['extension'];
$url_path = parse_url($_SERVER['REQUEST_URI'])['path'];

if ($extension !== 'xml') return;

// Определяем по URL тип текущего сайтмапа
$CURRENT_SITEMAP_KEY = null;
foreach (SITEMAP_LINKS as $sitemap_key => $sitemap_link) {
  if (strpos($url_path, $sitemap_link) !== false) {
    $CURRENT_SITEMAP_KEY = $sitemap_key;
    break;
  }
}
if (!$CURRENT_SITEMAP_KEY) return;


// -------------------------------------------------
// 1. Логика основного sitemap
// -------------------------------------------------

if ($CURRENT_SITEMAP_KEY === 'base') {
  // Формируем ссылки
  $output_links = SITEMAP_LINKS;

  // Если товаров больше лимита - Создаем ссылки с пагинацией на товары
  $products_count = smProducts::getCount();
  if ($products_count > PRODUCT_LIMIT) {
    unset($output_links['products']);
    $pages = $products_count / PRODUCT_LIMIT;
    foreach (range(1, $pages + 1) as $page) {
      $output_links["products-$page"] = "$page/" . SITEMAP_LINKS['products'];
    }
  }
  $output_links = array_values($output_links);

  // Формируем вывод
  $output = "";
  foreach ($output_links as $index => $xml_link) {
    $randomDate = smRandomDate($index);
    $output .= "<sitemap>
                  <loc>https://{$_SERVER['HTTP_HOST']}/$xml_link</loc>
                  <lastmod>$randomDate</lastmod>
                </sitemap>";
  }

  smPageOutput("<sitemapindex xmlns='http://www.sitemaps.org/schemas/sitemap/0.9'>$output</sitemapindex>");
}

// -------------------------------------------------
// 2. Логика sitemap на товары
// -------------------------------------------------

if ($CURRENT_SITEMAP_KEY === 'products') {
  $products_count = smProducts::getCount();

  if ($products_count > PRODUCT_LIMIT) {
    // Получили offset
    $parts = array_filter(explode('/', $url_path));
    $offset = array_values($parts)[0]; // После array_filter могут сбиться индексы и offset станет не нулевым, поэтому сброс индексов через array_values

    if (!is_numeric($offset)) return;

    $offset = ($offset - 1) * PRODUCT_LIMIT;

    $products = smProducts::getByOffset(PRODUCT_LIMIT, $offset);
  } else {
    $products = smProducts::getAll();
  }

  // Формируем вывод
  foreach ($products as $product) {
    $randomDate = smRandomDate($product['id']);
    $output .= "<url>
                  <loc>https://{$_SERVER['HTTP_HOST']}/{$product['uri']}</loc>
                  <lastmod>$randomDate</lastmod>
                  <changefreq>weekly</changefreq>
                  <priority>0.5</priority>
                </url>";
  }

  smPageOutput("<urlset xmlns='http://www.sitemaps.org/schemas/sitemap/0.9'>$output</urlset>");
}

// -------------------------------------------------
// 3. Логика sitemap на категории
// -------------------------------------------------

if ($CURRENT_SITEMAP_KEY === 'categories') {
  $categories = smCategories::getAll();

  // Формируем вывод
  foreach ($categories as $category) {
    $randomDate = smRandomDate($category['id']);
    $output .= "<url>
                  <loc>https://{$_SERVER['HTTP_HOST']}/{$category['uri']}</loc>
                  <lastmod>$randomDate</lastmod>
                  <changefreq>weekly</changefreq>
                  <priority>0.75</priority>
                </url>";
  }

  smPageOutput("<urlset xmlns='http://www.sitemaps.org/schemas/sitemap/0.9'>$output</urlset>");
}

// -------------------------------------------------
// 4. Логика sitemap на обычные ресурсы
// -------------------------------------------------

if ($CURRENT_SITEMAP_KEY === 'resources') {
  $resources = smResources::getAll();

  // Формируем вывод
  foreach ($resources as $resource) {
    $randomDate = smRandomDate($resource['id']);

    $uri = $resource['uri'];
    if ($uri === 'index/')  $uri = "";

    $output .= "<url>
                  <loc>https://{$_SERVER['HTTP_HOST']}/{$uri}</loc>
                  <lastmod>$randomDate</lastmod>
                  <changefreq>weekly</changefreq>
                  <priority>0.25</priority>
                </url>";
  }

  smPageOutput("<urlset xmlns='http://www.sitemaps.org/schemas/sitemap/0.9'>$output</urlset>");
}
