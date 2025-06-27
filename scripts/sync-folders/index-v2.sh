#!/bin/bash
# Переменные
SOURCE_SERVER="root@1.1.1.1"
SOURCE_SERVER_PASSWORD=""
SOURCE_PATH="/home/stroymarket/web/metallobaza-178.ru/public_html/assets/images/products/" # Откуда будет копирование
DEST_PATH="/home/user/web/metallobaza-178.ru/public_html/assets/images/products/" # Куда
LOG_FILE="sync_folders.log"

# Начало синхронизации
echo "Начало синхронизации всех папок и файлов"
echo "Синхронизация начата: $(date)" >> $LOG_FILE

# Копирование всех папок и файлов с первого сервера на второй сервер
if ! sshpass -p "${SOURCE_SERVER_PASSWORD}" rsync -avz --info=progress2 -e "sshpass -p ${SOURCE_SERVER_PASSWORD} ssh -o StrictHostKeyChecking=no" "${SOURCE_SERVER}:${SOURCE_PATH}/" "${DEST_PATH}/" | tee -a $LOG_FILE; then
    echo "Синхронизация завершилась с ошибкой"
    echo "Ошибка при синхронизации: $(date)" >> $LOG_FILE
else
    echo "Синхронизация успешно завершена"
    echo "Синхронизация завершена: $(date)" >> $LOG_FILE
fi

echo "Синхронизация завершена."
