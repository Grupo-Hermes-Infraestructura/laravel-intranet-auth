# Easy GHI Intranet Authentication with Laravel

## Installation

First, pull in the package through Composer.

```javascript
"require": {
    "ghi/laravel-intranet-auth": "~1.0"
}
```

If using Laravel 5, include the service provider within `app/config/app.php`.

```php
'providers' => [
    Ghi\IntranetAuth\IntranetAuthServiceProvider::class
];
```

Then you must configure the authentication driver within `app/config/auth.php`.

```php
    'driver' => 'ghi-intranet',
```

Now, Laravel uses a default `User` model for authentication, you still can use this model, just change the `AuthenticatableUser` trait for `AuthenticatableIntranetUser`.

```php
// app/Model.php

use Ghi\IntranetAuth\AuthenticatableIntranetUser;

class User extends Model implements AuthenticatableContract, CanResetPasswordContract
{
        use AuthenticatableIntranetUser, CanResetPassword;

        //
}
```

This package comes with a `Model` class that represents the Ghi intranet user.
This model is preconfigured to map users from the Ghi database, so no need to create your own, just change the `model` key value within `config/auth.php`.

```php
    'model' => Ghi\IntranetAuth\User::class,
```

## Usage

This package comes with a prebuilt login form view.

To use it just create your own login blade template and within include:

```php
@extends('app')

@section('content')
    @include(igh::login)
@stop
```

The form includes 3 inputs:
- usuario
- clave
- remember_me

This will be sent to your auth controller. Then you need to change the Laravel default postLogin method to accept this inputs.

```php
// app/Http/Controllers/AuthController.php

public function postLogin(Request $request)
{
    $this->validate($request, [
        'usuario' => 'required', 'clave' => 'required',
    ]);

    $credentials = $request->only('usuario', 'clave');

    if (auth()->attempt($credentials, $request->has('remember_me'))) {

        return redirect('/home');
    }

    return redirect($this->loginPath())
        ->withInput($request->only('usuario', 'remember_me'))
        ->withErrors([
            'usuario' => 'Usuario o clave invalidos.',
        ]);
}
```