
#!/bin/bash

# Переменные
SOURCE_SERVER="askulikov@77.222.61.130"
SOURCE_SERVER_PASSWORD=""
SOURCE_PATH="public_html/test/public_html/" # От куда будет копирование
DEST_PATH="/home/velesark/web/velesark-prod/public_html/" # Куда

# Массив папок для синхронизации через пробел
FOLDERS=("calculator" "connectors" "editor" "manager" "template" "uploads")

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