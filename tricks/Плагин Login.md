- После регистрации отправляется ссылка на подтверждение `https://horton/register-confirm/?lp=eHg0WlVlU21icA&amp;lu=dGVzdDU1QG1haWwucnU` и тут экранирование `&` все ломает. При получении переменных `lu` не будет получен. В файл `C:\OSPanel\home\horton\core\components\login\processors\register.php` добавил `$confirmUrl = str_replace('&amp;', '&', $confirmUrl);`

```php
/* generate confirmation url */
if ($this->login->inTestMode) {
    $confirmUrl = $this->modx->makeUrl(1, '', $confirmParams, 'full');
} else {
    $confirmUrl = $this->modx->makeUrl($this->controller->getProperty('activationResourceId', 1), '', $confirmParams, 'full');
    $confirmUrl = str_replace('&amp;', '&', $confirmUrl);
}
```
