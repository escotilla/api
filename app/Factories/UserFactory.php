<?php

namespace App\Factories;

use App\Business;
use App\Role;
use App\User;

class UserFactory
{
    public static function register(string $email, string $password, string $name)
    {
        $user = self::newUser($email, $password, $name);

        $business = new Business();
        $user->business()->associate($business);

        $role = Role::where('name', 'applicant')->first();
        $user->role()->associate($role);
        $user->save();

        return $user;
    }

    public static function newUser(string $email, string $password, string $name) {
        $user = new User();
        $user->username = $email;
        $user->email = $email;
        $user->name = $name;
        $user->password = password_hash($password, PASSWORD_BCRYPT);
        $user->api_token = uniqid('', true);
        $user->login_attempts = 0;

        return $user;
    }
}
