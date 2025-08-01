import os
import csv
import json

# Названия папок
folders = ['web']

# Создание папок, если их нет
for folder in folders:
    os.makedirs(folder, exist_ok=True)

# Чтение CSV
csv_file = 'data.csv'  # Название CSV-файла
with open(csv_file, mode='r', encoding='utf-8') as f:
    reader = csv.DictReader(f)
    for row in reader:
        file_name = row['file_name']  # основное имя из CSV

        for folder in folders:
            # Формируем имя файла
            if folder == 'web':
                json_name = file_name
            else:
                json_name = f"{folder}-{file_name}"

            json_file_name = f"{json_name}.json"
            file_path = os.path.join(folder, json_file_name)

            # Добавляем поле host_name
            data = dict(row)  # копируем словарь
            data['host_name'] = folder

            # Сохраняем JSON
            with open(file_path, mode='w', encoding='utf-8') as json_file:
                json.dump(data, json_file, ensure_ascii=False, indent=2)

print("✅ JSON-файлы с полем host_name успешно созданы.")
