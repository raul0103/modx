{extends "file:modules/uploading-documents/layouts/base.layout.tpl"}

{block 'properties'}
    {set $title = "Прикрепите файл проекта"}
    {set $buttons_title = [
        "callback" => "Заказать звонок",
        "submit" => "Отправить"
    ]}
{/block}