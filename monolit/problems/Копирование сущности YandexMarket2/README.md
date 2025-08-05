Создает копию фида в яндексмаркете

1. в файле core\components\yandexmarket2\controllers\home.class.php изменить функцию getTemplateFile:

```php
    public function getTemplateFile(): string
    {
        $this->content .= '
        <div style=" padding: 20px; border-bottom: 1px solid #32ab9a; ">
            <button onclick="createCopyFid()" style="background: #32ab9a; color: #fff; border: none; border-radius: 4px; padding: 10px 20px; cursor: pointer;">Создать копию</button>
        </div>
        <script>
            window.createCopyFid = () => {
              const fidId = prompt("ID копируемого фида");
              if (!fidId) return;

              const params = new URLSearchParams({ fid_id: fidId });

              fetch("/assets/components/yandexmarket2/fid-clone.php", {
                method: "POST",
                headers: {
                  "Content-Type": "application/x-www-form-urlencoded; charset=UTF-8",
                },
                body: params.toString(),
              })
                .then(response => {
                  if (!response.ok) {
                    throw new Error(`HTTP ${response.status}`);
                  }
                  return response.text();
                })
                .then(text => {
                  console.log("Ответ сервера:", text);
                  location.reload();
                })
                .catch(err => {
                  console.error("Ошибка при запросе:", err);
                });
            };
        </script>
        <div id="yandexmarket-app"></div>';
        return '';
    }
```
