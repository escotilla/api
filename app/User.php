<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;
use Jenssegers\Mongodb\Relations\EmbedsMany;
use Jenssegers\Mongodb\Relations\EmbedsOne;
use Jenssegers\Mongodb\Relations\HasMany;

/**
 * Class User
 * @package App
 * @property string name
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
        'email',
        'username',
        'api_token',
        'login_attempts',
        'password',
        'dob'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }

    public function answers(): EmbedsMany
    {
        return $this->embedsMany(Answer::class);
    }

    public function business(): EmbedsOne
    {
        return $this->embedsOne(Business::class);
    }

    public function uploaded_files(): HasMany
    {
        return $this->hasMany(UploadedFile::class);
    }

    public function to_auth_output(): array
    {
        $output = [
            'email' => $this->email,
            'api_token' => $this->api_token,
            'user_id' => $this->_id,
            'application_ids' => $this->applications->map(function(Application $application) {
                return $application->_id;
            }),
            'uploaded_files' => $this->uploaded_files->map(function(UploadedFile $file) {
                return $file->to_public_output();
            })
        ];

        return $output;
    }

    public function to_application_output()
    {
        return $this->applications->map(function($application) {
            return $application->to_public_output();
        });
    }
}
