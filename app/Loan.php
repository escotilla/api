<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;
use Jenssegers\Mongodb\Relations\BelongsTo;

/**
 * Class User
 * @package App
 * @property float principal
 * @property string term
 * @property string frequency
 * @property float interest_rate
 * @property string payout_id
 * @property string status
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
        'interest_rate',
        'payout_id',
        'interest_rate',
        'status'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }

    public function to_public_output(): array
    {
        $output = [
            'id' => $this->_id,
            'principal' => $this->principal,
            'term' => $this->term,
            'frequency' => $this->frequency,
            'payout_id' => $this->payout_id,
            'interest_rate' => $this->interest_rate,
            'application_id' => $this->application_id,
            'user_id' => $this->user_id,
            'status' => $this->status,
        ];

        return $output;
    }
}
