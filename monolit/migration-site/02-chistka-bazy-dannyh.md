# 2. Чистка базы данных

После переноса файлов необходимо очистить базу данных от лишних данных.

> ⚠️ **Внимание:** Перед выполнением SQL-запросов обязательно создайте резервную копию базы данных!

## Ресурсы

### Удаление ресурсов по контекстам

Удаляем все ресурсы, которые не относятся к нужным контекстам.

<details>
<summary>Показать код</summary>

```sql
DELETE FROM modx_site_content WHERE context_key NOT IN ('tizol','beltep','baswool','xotpipe','isotecti','ruspanel')
```

</details>

### Товары

Удаляем товары, которые не связаны с существующими ресурсами.

```sql
DELETE FROM modx_ms2_products WHERE id NOT IN (SELECT id FROM modx_site_content)
```

### TV (Template Variables)

Очищаем значения TV-полей для удаленных ресурсов.

```sql
DELETE FROM modx_site_tmplvar_contentvalues WHERE contentid NOT IN (SELECT id FROM modx_site_content)
```

### Опции товаров

Удаляем опции товаров для несуществующих товаров.

```sql
DELETE FROM modx_ms2_product_options WHERE product_id NOT IN (SELECT id FROM modx_site_content)
```

### Опции категорий

Удаляем опции категорий для несуществующих категорий.

```sql
DELETE FROM modx_ms2_category_options WHERE category_id NOT IN (SELECT id FROM modx_site_content)
```

### Products INTRO

Очищаем интротексты для удаленных ресурсов.

```sql
DELETE FROM modx_mse2_intro WHERE resource NOT IN (SELECT id FROM modx_site_content)
```

### Галереи товаров

Удаляем файлы галерей для несуществующих товаров.

```sql
DELETE FROM modx_ms2_product_files WHERE product_id NOT IN (SELECT id FROM modx_site_content)
```

</details>

## Контексты

> ⚠️ **Важно:** Не трогать контексты **mgr** и **web**!

### Удаление лишних контекстов

<details>
<summary>Показать код</summary>

#### Лишние контексты

```sql
DELETE FROM modx_context WHERE `key` NOT IN ('web','mgr','tizol','beltep','baswool','xotpipe','isotecti','ruspanel')
```

#### Настройки контекстов

```sql
DELETE FROM modx_context_setting WHERE context_key NOT IN ('web','mgr','tizol','beltep','baswool','xotpipe','isotecti','ruspanel')
```

#### Разрешения

```sql
DELETE FROM modx_access_context WHERE `target` NOT IN ('web','mgr','tizol','beltep','baswool','xotpipe','isotecti','ruspanel')
```

</details>

