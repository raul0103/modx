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