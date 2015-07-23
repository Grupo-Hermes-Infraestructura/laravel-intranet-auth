# Autenticacion Intranet GHI con Laravel

## Instalación

Primero, instalar el paquete a través de composer.

```javascript
"require": {
    "ghi/laravel-intranet-auth": "~1.0"
}
```

Si estas usando Laravel 5.*, incluye el service provider dentro de `app/config/app.php`.

```php
'providers' => [
    Ghi\IntranetAuth\IntranetAuthServiceProvider::class
];
```

Ahora se debe configurar el driver de autenticacion dentro de `app/config/auth.php`.

```php
    'driver' => 'ghi-intranet',
```

Laravel usa el model `User` para autenticación, aun puedes seguir usando este modelo, solo cambia el `AuthenticatableUser` trait por `AuthenticatableIntranetUser`.

```php
// app/Model.php

use Ghi\IntranetAuth\AuthenticatableIntranetUser;

class User extends Model implements AuthenticatableContract, CanResetPasswordContract
{
    use AuthenticatableIntranetUser, CanResetPassword;

    //
}
```

Este paquete incluye un modelo `User` que representa un usuario de la intranet Ghi.
El modelo esta preconfigurado para usarse directamente con los usuarios de la intranet.
En caso de que requieras la funcionalidad minima de este modelo, lo puedes usar para evitar configurar el que viene con Laravel.

Para usarlo, solo tienes que cambiar la clave `model` dentro de `app/config/auth.php`.

This package comes with a `Model` class that represents the Ghi intranet user.
This model is preconfigured to map users from the Ghi database, so no need to create your own, just change the `model` key value within `config/auth.php`.

```php
    'model' => Ghi\IntranetAuth\User::class,
```

## Uso

Este paquete incluye una vista predefinida para hacer el login.

Para usarla, solo crea tu propia plantilla de login con blade y dentro de esta incluye lo siguiente:

```php
    @include(ghi::login)
```

El formulario de esta vista incluye 3 campos:
- usuario
- clave
- remember_me

Estos datos seran enviados a tu controlador de autenticación (AuthController).

Despues en tu controlador de autenticación, reemplaza el trait `AuthenticatesAndRegistersUsers` por `AuthenticatesIntranetUsers`

```php
// app/Http/Controllers/AuthController.php

use Ghi\IntranetAuth\AuthenticatesIntranetUsers;

class AuthController extends Controller
{
    use AuthenticatesIntranetUsers, ThrottlesLogins;
    
    //
}
```

Este trait incluye los metodos `postLogin` y `getLogout` predefinidos para autenticar y cerrar sesión.

Puedes personalizar la ruta donde sera dirigido el usuario después de una autenticación correcta.
Solo tienes que agregar esta propiedad en el controlador de autenticación:

```php
    protected $redirectPath = '/home';
```

Finalmente, define las rutas para autenticacion dentro de `app/Http/routes.php`

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