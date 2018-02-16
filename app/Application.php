<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;
use Jenssegers\Mongodb\Relations\BelongsTo;
use Jenssegers\Mongodb\Relations\EmbedsMany;
use Jenssegers\Mongodb\Relations\HasMany;
use Jenssegers\Mongodb\Relations\HasOne;

/**
 * Class Application
 * @package App
 * @property string status
 * @property string name
 * @property string email
 */
class Application extends Eloquent
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'amount',
        'term',
        'frequency',
        'amount',
        'status',
        'name',
        'email'
    ];

    public function updateChecklist($type, $status = 'complete') {
            $checklistItem = $this
                ->checklist()
                ->first(function($item)  use ($type) {
                    return $item->title == $type;
                });

            if (isset($checklistItem) && $checklistItem->getStatus() !== $status) {
                $checklistItem->setStatus($status);
                $checklistItem->save();
            }
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function answers(): EmbedsMany
    {
        return $this->embedsMany(Answer::class);
    }

    public function checklist(): EmbedsMany
    {
        return $this->embedsMany(ChecklistItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(LoanPayment::class);
    }

    public function loan(): HasOne
    {
        return $this->hasOne(Loan::class);
    }

    public function to_public_output(): array
    {
        $loan = is_null($this->loan) ? '' : $this->loan->_id;

        $output = [
            'id' => $this->_id,
            'status' => $this->status,
            'answers' => $this->answers->mapWithKeys(function (Answer $answer) {
                return [$answer->question->_id => $answer->answer];
            }),
            'checklist' => $this->checklist->map(function($item) {
                return $item->to_public_output();
            }),
            'loan_id' => $loan,
            'payments' => $this->payments->map(function(LoanPayment $payment) {
                return $payment->to_public_output();
            })
        ];

        return $output;
    }
}
