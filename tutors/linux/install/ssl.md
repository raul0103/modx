1. Создать установщик

```bash
#!/bin/bash

# Установка Certbot и модуля Apache
sudo apt update
sudo apt install -y certbot python3-certbot-apache

# Настройка HTTPS через Let's Encrypt
# ⚠️ Убедись, что домен указывает на IP сервера перед запуском
DOMAIN="parser.mlt-services.ru"
EMAIL="rshakurov95@mail.ru" # ← укажи свой email

# Получение сертификата
sudo certbot --apache -d $DOMAIN --agree-tos --email $EMAIL --redirect --non-interactive

# Проверка cron задачи (возможно, уже добавлена certbot)
sudo systemctl list-timers | grep certbot

# Добавим принудительно, если нет
(crontab -l 2>/dev/null; echo "0 3 * * * certbot renew --quiet") | crontab -

echo "SSL сертификат установлен и автообновление настроено!"
```

2. Сделать исполняемым

```bash
chmod +x ssl-setup.sh
```

3. Certbot сам создает файлы /etc/apache2/sites-available/site-1-le-ssl.conf и включает HTTPS.

   - В нем исправить пути к сайту

4. Установка на другие сайты

```bash
sudo certbot install --cert-name parser.mlt-services.ru
```
