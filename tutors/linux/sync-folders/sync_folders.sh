#!/bin/bash

# Переменные
SOURCE_SERVER="askulikov@77.222.61.130"
SOURCE_SERVER_PASSWORD=""
SOURCE_PATH="public_html/test/public_html/assets/images/" # Откуда копировать
DEST_PATH="/home/velesark/web/velesark-prod/public_html/assets/images/" # Куда копировать

# Массив папок для синхронизации
FOLDERS=("products")

# Проверка наличия необходимых утилит
if ! command -v sshpass &> /dev/null; then
    echo "Ошибка: sshpass не установлен. Установите его с помощью 'sudo apt install sshpass'."
    exit 1
fi

if ! command -v rsync &> /dev/null; then
    echo "Ошибка: rsync не установлен. Установите его с помощью 'sudo apt install rsync'."
    exit 1
fi

# Создание директории назначения, если она не существует
mkdir -p "${DEST_PATH}" || {
    echo "Ошибка: Не удалось создать директорию ${DEST_PATH}"
    exit 1
}

# Копирование папок с первого сервера на второй сервер
echo "Начало синхронизации папок"
for folder in "${FOLDERS[@]}"; do
    echo "Копирование папки ${folder}"
    
    # Выполнение rsync с выводом скопированных файлов и подпапок
    rsync_output=$(sshpass -p "${SOURCE_SERVER_PASSWORD}" rsync -avz --progress \
        -e "sshpass -p ${SOURCE_SERVER_PASSWORD} ssh -o StrictHostKeyChecking=no" \
        "${SOURCE_SERVER}:${SOURCE_PATH}/${folder}/" "${DEST_PATH}/${folder}/" 2>&1)
    
    # Проверка статуса выполнения команды
    if [ $? -eq 0 ]; then
        echo "Успешно скопирована папка ${folder}"
        # Вывод списка скопированных файлов и подпапок
        echo "Скопированные файлы и подпапки:"
        echo "${rsync_output}" | grep -E "^[a-zA-Z0-9]" | grep -v "sending incremental file list"
    else
        echo "Ошибка при копировании папки ${folder}"
        echo "${folder}: ${rsync_output}" >> sync_folders.log
        echo "Подробности ошибки записаны в sync_folders.log"
    fi
done

echo "Синхронизация завершена."