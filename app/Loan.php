<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;
use Jenssegers\Mongodb\Relations\BelongsTo;

/**
 * Class User
 * @package App
 * @property string application_id
 */
class Loan extends Eloquent
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'principal',
        'term',
        'frequency',
        'amount',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo('User');
    }

    public function to_auth_ouput(): array
    {
        $output = [
            'application_id' => $this->application_id
        ];

        return $output;
    }
}
