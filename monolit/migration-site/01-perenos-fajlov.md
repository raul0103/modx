# 1. Перенос файлов

Переносим без лишних тяжелых файлов.

## ⚠️ Важно

Исключаем из переноса временные и кэш-файлы, а также большие директории с изображениями, которые будут перенесены отдельно.

## Команда rsync

<details>
<summary>Показать код</summary>

```bash
rsync -av \
  --exclude '_src/' \
  --exclude '_import/' \
  --exclude 'assets/template/img/import' \
  --exclude 'assets/template/img/pdf-to-jpg' \
  --exclude 'assets/cache_image' \
  --exclude 'assets/yandexmarket' \
  --exclude 'assets/images/products' \
  --exclude 'assets/images/imgWithWatermark' \
  --exclude 'assets/uploads' \
  --exclude 'core/cache' \
  --exclude 'core/backups' \
  --exclude 'core/cache-banners' \
  --exclude 'core/custom-cache' \
  /home/minvata/web/path/public_html/ \
  root@111.111.111.65:/home/user/web/new_path/public_html/
```

</details>

## Исключаемые директории

- `_src/` — исходники
- `_import/` — временные файлы импорта
- `assets/template/img/import` — временные изображения
- `assets/template/img/pdf-to-jpg` — конвертированные изображения
- `assets/cache_image` — кэш изображений
- `assets/yandexmarket` — файлы для Яндекс.Маркета
- `assets/images/products` — изображения товаров (переносятся отдельно)
- `assets/images/imgWithWatermark` — изображения с водяными знаками
- `assets/uploads` — загруженные файлы
- `core/cache` — системный кэш
- `core/backups` — резервные копии
- `core/cache-banners` — кэш баннеров
- `core/custom-cache` — пользовательский кэш

