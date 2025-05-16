## Настройка сервера ubuntu

- Для добавления других сайтов используйте аналогию с site-1
- При создании пользователей БД используется '$MYSQL_USER'@'%' для доступа со всех IP и внешнего подключения

#### Настройки перед запуском

1. Изменить в bash скрипте "site-1" на домен сайта который будет привязан к серверу
2. Изменить данные для создания БД

#### Запуск

1. Создать файл

```bash
nano setup.sh
```

2. Сделать исполняемым

```bash
chmod +x setup.sh
```

3. Запустить

```
./setup.sh
```

## setup.sh

```bash
#!/bin/bash

# Обновление системы
sudo apt update && sudo apt upgrade -y
# Установка необходимых пакетов
sudo apt install -y apache2 mysql-server unzip curl software-properties-common
# Установка PHP 8.1
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update
sudo apt install -y php8.1 php8.1-cli php8.1-common php8.1-mysql php8.1-xml php8.1-bcmath php8.1-curl php8.1-mbstring
# Настройка MySQL
sudo mysql_secure_installation

# >>>>>>>>>>>>>>>>>>> Создание баз данных (пример)
#!/bin/bash
MYSQL_ROOT_PASSWORD="StrongRoot@123" # Пароль для root пользователя для настройки базы
MYSQL_USER="myuser2"
MYSQL_PASSWORD="StrongP@ssw0rd!"
MYSQL_DATABASE="mydatabase2"
# Сброс политики паролей на LOW (опционально)
sudo mysql -u root -p$MYSQL_ROOT_PASSWORD -e "SET GLOBAL validate_password.policy=LOW;"
sudo mysql -u root -p$MYSQL_ROOT_PASSWORD -e "SET GLOBAL validate_password.length=6;"
# Создание базы данных
sudo mysql -u root -p$MYSQL_ROOT_PASSWORD -e "CREATE DATABASE IF NOT EXISTS $MYSQL_DATABASE;"
# Создание пользователя
sudo mysql -u root -p$MYSQL_ROOT_PASSWORD -e "CREATE USER IF NOT EXISTS '$MYSQL_USER'@'%' IDENTIFIED BY '$MYSQL_PASSWORD';"
# Назначение прав
sudo mysql -u root -p$MYSQL_ROOT_PASSWORD -e "GRANT ALL PRIVILEGES ON $MYSQL_DATABASE.* TO '$MYSQL_USER'@'%' WITH GRANT OPTION;"
sudo mysql -u root -p$MYSQL_ROOT_PASSWORD -e "FLUSH PRIVILEGES;"
# Открытие порта 3306
sudo ufw allow 3306/tcp
sudo ufw reload

echo "MySQL пользователь и база созданы успешно!"
# <<<<<<<<<<<<<<<<<<<<<<<

# Создание директорий для сайтов
sudo mkdir -p /var/www/site-1/public
# Назначение прав
sudo chown -R $USER:www-data /var/www/site-1
sudo chmod -R 775 /var/www/site-1
# Создание тестового файла index.php
echo "<?php phpinfo(); ?>" | sudo tee /var/www/site-1/public/index.php

# Конфигурация виртуальных хостов
cat << EOF | sudo tee /etc/apache2/sites-available/site-1.conf
<VirtualHost *:80>
    ServerName site1.ru
    DocumentRoot /var/www/site-1/public

    <Directory /var/www/site-1/public>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    <Directory /var/www/site-1>
        Require all denied
    </Directory>

    ErrorLog \${APACHE_LOG_DIR}/site-1_error.log
    CustomLog \${APACHE_LOG_DIR}/site-1_access.log combined
</VirtualHost>
EOF

# Активация виртуальных хостов и модулей
sudo a2ensite site-1.conf
sudo a2enmod rewrite
# Отключаем дефолтный сайт
sudo a2dissite 000-default.conf
# Разрешение удаленных подключений
sudo sed -i 's/bind-address\s*=\s*127.0.0.1/bind-address = 0.0.0.0/' /etc/mysql/mysql.conf.d/mysqld.cnf
# Перезапуск MySQL
sudo systemctl restart mysql
# Перезапуск Apache
sudo systemctl restart apache2
# Вывод результата
echo "Сервер настроен! Добавьте записи в /etc/hosts для site1.local и site2.local"
```
