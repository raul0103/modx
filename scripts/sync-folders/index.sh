
#!/bin/bash

# Переменные
SOURCE_SERVER="root@1.1.1.1"
SOURCE_SERVER_PASSWORD=""
SOURCE_PATH="/home/minvata/web/minvata.dev.skderdom.beget.tech/public_html/assets/images/products/" # От куда будет копирование
DEST_PATH="/home/user/web/krasnodar.krovlyasp.ru/public_html/assets/images/products/" # Куда

# Массив папок для синхронизации
FOLDERS=("92922" "92923")

# Копирование папок с первого сервера на второй сервер
LOG_FILE="sync_folders.log"

# Очищаем лог в начале (если нужно)
# > "$LOG_FILE"

echo "Начало синхронизации папок" | tee -a "$LOG_FILE"
for folder in "${FOLDERS[@]}"; do
    echo "Копирование папки ${folder}" | tee -a "$LOG_FILE"
    
    if ! timeout 10 sshpass -p "${SOURCE_SERVER_PASSWORD}" rsync -avz -e "sshpass -p ${SOURCE_SERVER_PASSWORD} ssh -o StrictHostKeyChecking=no" \
        "${SOURCE_SERVER}:${SOURCE_PATH}/${folder}/" "${DEST_PATH}/${folder}/" >> "$LOG_FILE" 2>&1; then
        
        echo "Ошибка при копировании папки ${folder}" | tee -a "$LOG_FILE"
        echo "${folder}" >> "$LOG_FILE"
    else
        echo "Успешно" | tee -a "$LOG_FILE"
    fi
done
echo "Синхронизация завершена." | tee -a "$LOG_FILE"
