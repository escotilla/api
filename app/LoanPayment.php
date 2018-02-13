<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;
use Jenssegers\Mongodb\Relations\BelongsTo;

/**
 * Class Payment
 * @package App
 * @property float amount
 * @property string transaction_id
 * @property string payment_id
 * @property string status
 * @property bool success
 */
class LoanPayment extends Eloquent
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'amount',
        'payment_id',
        'success',
        'status'
    ];

    public function setAmount($amount)
    {
        $this->amount = $amount;
        return $this;
    }

    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    public function setPaymentId($payment_id)
    {
        $this->payment_id = $payment_id;
        return $this;
    }

    public function setSuccess($success)
    {
        $this->success = $success;
        return $this;
    }

    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function to_public_output() {
        $output = [
            'success' => $this->success,
            'status' => $this->status,
            'amount' => $this->amount
        ];

        return $output;
    }
}