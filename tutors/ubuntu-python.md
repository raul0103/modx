## apache

1. `sudo apt update`
2. `sudo apt install apache2`

## flask

1. `sudo apt-get install python3 python3-pip python3-venv`
2. `pip3 install flask`
3. `cd /var/www/html && python3 -m venv flask-venv` // Переходим в папку с проектом
4. `source flask-venv/bin/activate`
5. `sudo nano app.py` // Сначала скрипт через Selenium

   ```
    from flask import Flask
    app = Flask(__name__)

    @app.route('/')
    def index():
        return "Hello, World!"
   ```

6. `flask run --host=0.0.0.0` // Тест проекта по порту :5000. порт должен быть открыт

## WSGI (Настройка Flask на Apache)

### 1. Установка модуля WSGI для Python 3

```bash
sudo apt-get install libapache2-mod-wsgi-py3
```

### 2. Создание WSGI-файла

```bash
sudo nano /var/www/html/flask-app.wsgi
```

**Содержимое файла:**

```python
import sys
import os
#sys.stderr = sys.stdout  # Чтобы ошибки выводились в лог

# Укажите путь к вашему проекту
sys.path.insert(0, '/var/www/html')

# Указываем путь к интерпретатору Python виртуального окружения
activate_this = '/var/www/html/flask-venv/bin/python'

# Устанавливаем переменную окружения PYTHONPATH на путь к вашему проекту
os.environ['PYTHONPATH'] = '/var/www/html'

# Импортируем ваше приложение Flask
from app import app as application
```

### 3. Настройка Apache-конфига

```bash
sudo nano /etc/apache2/sites-available/flask.conf
```

**Содержимое файла:**

Указать свой ServerName (домен либо IP)

```apache
<VirtualHost *:80>
    ServerName 185.207.1.100
    DocumentRoot /var/www/html

    # Настройка WSGI
    WSGIDaemonProcess app user=www-data group=www-data threads=5 python-home=/var/www/html/flask-venv
    WSGIScriptAlias / /var/www/html/flask-app.wsgi

    # Логи
    ErrorLog ${APACHE_LOG_DIR}/flask-error.log
    CustomLog ${APACHE_LOG_DIR}/flask-access.log combined

    <Directory /var/www/html>
        Options +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

### 4. Активация сайта

```bash
sudo a2ensite flask.conf
```

### 6. Перезагрузка Apache

```bash
sudo systemctl restart apache2
```

### 7. Просмотр логов

```bash
sudo tail -f /var/log/apache2/error.log
```
