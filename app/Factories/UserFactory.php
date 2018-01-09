<?php

namespace App\Factories;

use App\Business;
use App\User;

class UserFactory
{
    public static function register(string $email, string $password)
    {
        $user = new User();
        $user->username = $email;
        $user->email = $email;
        $user->password = password_hash($password, PASSWORD_BCRYPT);
        $user->api_token = uniqid('', true);
        $user->login_attempts = 0;

        $business = new Business();
        $user->business()->associate($business);

        return $user;
    }
}
