<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

/**
 * Class User
 * @package App
 * @property string name
 * @property string user_id
 * @property string username
 * @property string email
 * @property string password
 * @property string api_token
 * @property int login_attempts
 */
class User extends Eloquent
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'email',
        'username',
        'api_token',
        'login_attempts',
        'password'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];

    public function to_auth_output(): array
    {
        $output = [
            'email' => $this->email,
            'api_token' => $this->api_token,
            'user_id' => $this->user_id
        ];

        return $output;
    }
}
