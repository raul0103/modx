## Дружим sanctum + запросы axios

1. В моделе User добавить HasApiTokens. Добавляет методы (создания, удаления и тд) для работы с токенами

```php
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

```

2. После авторизации генерируем токен, и передаем его на след страницу

```php
        if (Auth::attempt($credentials)) {
            $user = auth()->user();
            $token = $user->createToken('auth_token')->plainTextToken;
            return redirect()->route('home')->with('token', $token);
        }
```

3. Получаем переданный токен и записываем в localStorage

```blade
@section('scripts')
    @if(session('token'))
        <script>
            localStorage.setItem("auth_token", "{{ session('token') }}");
            console.log("auth_token save");
        </script>
    @endif
@endsection
```

4. Такой axios

```js
import axios from "axios";

// Создаем экземпляр Axios
const token = localStorage.getItem("auth_token");
const axiosApi = axios.create({
  baseURL: "/api",
  headers: {
    Authorization: `Bearer ${token}`,
  },
});

export default axiosApi;
```
