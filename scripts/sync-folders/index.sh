
#!/bin/bash

# Переменные
SOURCE_SERVER="root@1.1.1.1"
SOURCE_SERVER_PASSWORD=""
SOURCE_PATH="/home/stroymarket/web/alterteplo.ru/public_html/assets/images/products/" # От куда будет копирование
DEST_PATH="/home/stroymarket-krd/web/krovlyashop-krasnodar.ru/public_html/assets/images/products/" # Куда

# Массив папок для синхронизации
FOLDERS=("127960" "127961" "127962")

# Копирование папок с первого сервера на второй сервер
echo "Начало синхронизации папок"
for folder in "${FOLDERS[@]}"; do
    echo "Копирование папки ${folder}"
    if ! timeout 10 sshpass -p "${SOURCE_SERVER_PASSWORD}" rsync -avz -e "sshpass -p ${SOURCE_SERVER_PASSWORD} ssh -o StrictHostKeyChecking=no" "${SOURCE_SERVER}:${SOURCE_PATH}/${folder}/" "${DEST_PATH}/${folder}/" > /dev/null 2>&1; then
        echo "${folder}" >> sync_folders.log
        echo "Ошибка"
    else
        echo "Успешно"
    fi
done
echo "Синхронизация завершена."