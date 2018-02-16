<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;
use Jenssegers\Mongodb\Relations\BelongsTo;
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
        'dob',
        'name'
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

    public function business(): EmbedsOne
    {
        return $this->embedsOne(Business::class);
    }

    public function uploaded_files(): HasMany
    {
        return $this->hasMany(UploadedFile::class);
    }

    public function loans(): HasMany
    {
        return $this->hasMany(Loan::class);
    }

    public function role(): BelongsTo
    {
        return $this->BelongsTo(Role::class);
    }

    public function isAdmin()
    {
        return $this->role->name === 'admin';
    }

    public function payments(): HasMany
    {
        return $this->hasMany(LoanPayment::class);
    }

    public function to_auth_output(): array
    {
        $loans = is_null($this->loans) ? [] : $this->loans->map(function(Loan $loan) {
          return $loan->to_public_output();
        });

        $output = [
            'email' => $this->email,
            'api_token' => $this->api_token,
            'id' => $this->_id,
            'applications' => $this->applications->map(function (Application $application) {
                return $application->to_public_output();
            }),
            'uploaded_files' => $this->uploaded_files->map(function (UploadedFile $file) {
                return $file->to_public_output();
            }),
            'loans' => $loans,
            'role' => $this->role->getName()
        ];

        return $output;
    }

    public function to_application_output()
    {
        return $this->applications->map(function ($application) {
            return $application->to_public_output();
        });
    }
}
