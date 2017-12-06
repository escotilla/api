<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

/**
 * Class User
 * @package App
 * @property string application_id
 */
class Application extends Eloquent
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'application_id',
    ];

    public function to_auth_ouput(): array
    {
        $output = [
            'application_id' => $this->application_id
        ];

        return $output;
    }
}
