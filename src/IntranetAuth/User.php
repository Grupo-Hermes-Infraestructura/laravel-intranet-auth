<?php

namespace Ghi\IntranetAuth;

use Ghi\Core\Models\User as BaseUser;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

class User extends BaseUser implements AuthenticatableContract, CanResetPasswordContract
{
    use AuthenticatableIntranetUser, CanResetPassword;
}
