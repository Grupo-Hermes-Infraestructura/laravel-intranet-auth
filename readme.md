# Autenticación Intranet GHI con Laravel

## Instalación

Primero, instalar el paquete a través de composer.

```javascript
"require": {
    "ghi/laravel-intranet-auth": "~1.0"
}
```

Si estas usando Laravel 5.*, incluye el service provider dentro de `config/app.php`.

```php
'providers' => [
    Ghi\IntranetAuth\IntranetAuthServiceProvider::class
];
```

## Configuración

### Driver de Autenticación

Se debe cambiar la clave `driver` dentro de `config/auth.php`.

```php
    'driver' => 'ghi-intranet',
```

### Modelo de Autenticación

Laravel utiliza el modelo `app/User` para autenticación, aun puedes seguir usando este modelo, solo cambia el `AuthenticatableUser` trait por `AuthenticatableIntranetUser`.

```php
// app/Model.php

use Ghi\Core\App\Auth\AuthenticatableIntranetUser;

class User extends Model implements AuthenticatableContract, CanResetPasswordContract
{
    use AuthenticatableIntranetUser, CanResetPassword;

    //
}
```

Este paquete incluye un modelo `User` que representa un usuario de la intranet Ghi.
En caso de que requieras la funcionalidad minima de este modelo, lo puedes usar para evitar configurar el que viene con Laravel.
El modelo esta pre-configurado para usarse directamente con los usuarios de la intranet.

Para usarlo, solo tienes que cambiar la clave `model` dentro de `config/auth.php`.

```php
    'model' => Ghi\Core\Models\User::class,
```

## Uso

### Controlador

Después en tu controlador de autenticación, reemplaza el trait `AuthenticatesAndRegistersUsers` por `AuthenticatesIntranetUsers`

```php
// app/Http/Controllers/AuthController.php

use Ghi\IntranetAuth\AuthenticatesIntranetUsers;

class AuthController extends Controller
{
    use AuthenticatesIntranetUsers, ThrottlesLogins;
    
    //
}
```

Este trait incluye los metodos `postLogin` y `getLogout` pre-definidos para autenticar y cerrar sesión.

Puedes personalizar la ruta donde sera dirigido el usuario después de una autenticación correcta.
Solo tienes que agregar esta propiedad en el controlador de autenticación:

```php
    protected $redirectPath = '/home';
```

### Vista

Este paquete incluye una vista predefinida que contiene un formulario con los campos necesarios para hacer un login, ademas la vista esta optimizada y lista para usar con bootstrap.

Para usarla, solo crea la vista `login.blade.php` en `resources/views/auth` y dentro de esta incluye lo siguiente:

```php
    @include('ghi::login')
```

El formulario de esta vista incluye 3 campos:
- usuario
- clave
- remember_me

Estos datos seran enviados a tu controlador de autenticación (AuthController).

Si requieres hacer algun cambio a esta vista, puedes publicarla con artisan:

```php
    php artisan vendor:publish
```

Esto copiara la vista `login.blade.php` que incluye el paquete en `resources/views/vendor/ghi` para que puedas hacerle los ajustes necesarios.

### Rutas

Finalmente, define las rutas para autenticación dentro de `app/Http/routes.php`

```php
    Route::get('auth/login', [
        'as' => 'auth.login',
        'uses' => 'Auth\AuthController@getLogin'
    ]);
    
    Route::post('auth/login', [
        'as' => 'auth.login',
        'uses' => 'Auth\AuthController@postLogin'
    ]);
    
    Route::get('auth/logout', [
        'as' => 'auth.logout',
        'uses' => 'Auth\AuthController@getLogout'
    ]);
```