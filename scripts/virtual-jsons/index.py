import csv
import json
import os

# Имя входного CSV файла
csv_file = "data.csv"
# Папка для сохранения JSON файлов
results_folder = "results"

# Читаем CSV файл и создаем JSON структуру
def csv_to_json(csv_filepath, results_folder):
    # Создаем папку, если она не существует
    if not os.path.exists(results_folder):
        os.makedirs(results_folder)
    
    try:
        with open(csv_filepath, mode="r", encoding="utf-8") as file:
            reader = csv.DictReader(file)
            
            for row in reader:
                # Извлекаем значение из поля "context"
                context = row.get("context", "").strip()
                if not context:
                    continue  # Пропускаем строки без значения в "context"
                
                # Создаем имя файла на основе значения context
                context_filename = f"{context}.json"
                context_filepath = os.path.join(results_folder, context_filename)
                
                # Добавляем в объект с модификацией "topo2" и "topo3"
                data = {
                    key.strip(): (
                        f"в {value.strip()}" if key.strip() == "topo2" else 
                        f"по {value.strip()}" if key.strip() == "topo3" else 
                        value.strip()
                    )
                    for key, value in row.items()
                    if key.strip() != "context" and value is not None
                }
                
                # Сохраняем данные в отдельный JSON файл
                with open(context_filepath, mode="w", encoding="utf-8") as json_file:
                    json.dump(data, json_file, ensure_ascii=False, indent=4)
                
                print(f"JSON файл '{context_filename}' успешно создан!")
    
    except FileNotFoundError:
        print(f"Файл '{csv_filepath}' не найден.")
    except Exception as e:
        print(f"Произошла ошибка: {e}")

# Выполняем преобразование
csv_to_json(csv_file, results_folder)
