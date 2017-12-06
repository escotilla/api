<?php

namespace App\Http\Controllers;

use App\Factories\UserFactory;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class UserController extends EscotillaController
{
    public function create(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $email = $request->input('email');
        $password = $request->input('password');

        $user = User::where('email', $email)->first();

        if ($user === null) {
            $user = UserFactory::register($email, $password);
            $user->save();
        } else if (!password_verify($password, $user->password)) {
            return $this->errorResponse('User already exists', Response::HTTP_UNAUTHORIZED);
        }

        return $this->successResponse($user->to_auth_output());
    }

    public function login(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $email = $request->input('email');
        $password = $request->input('password');

        $user = User::where('email', $email)->first();

        if ($user === null) {
            return $this->errorResponse('User does not exist', Response::HTTP_NOT_FOUND);
        } else if (!password_verify($password, $user->password)) {
            $user->increment('login_attempts');
            return $this->errorResponse('Incorrect password', Response::HTTP_UNAUTHORIZED);
        }

        return $this->successResponse($user->to_auth_output());
    }
}