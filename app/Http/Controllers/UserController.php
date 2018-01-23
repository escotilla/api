<?php

namespace App\Http\Controllers;

use App\Factories\UserFactory;
use App\Role;
use App\User;
use Gate;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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

    public function read(Request $request) {
        $userId = $request->input('userId');
        $applicant = Role::where('name', 'applicant')->first();

        if (is_null($userId)) {
            $limit = 25;
            $projection = ['id', 'email', 'username', 'name', 'applications.status'];
            $users = DB::collection('users')
                ->where('role_id', $applicant->_id)
                ->paginate($limit, $projection);

            return $this->successResponse($users);
        }

        $user = User::find($userId);

        if (is_null($user)) {
            return $this->errorResponse('User not found', Response::HTTP_NOT_FOUND);
        }

        return $this->successResponse($user->to_auth_output());
    }
}