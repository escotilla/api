<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;
use Jenssegers\Mongodb\Relations\BelongsTo;
use Jenssegers\Mongodb\Relations\EmbedsMany;
use Jenssegers\Mongodb\Relations\HasMany;

/**
 * Class Application
 * @package App
 * @property string status
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
        'status'
    ];

    public function answers(): EmbedsMany
    {
        return $this->embedsMany(Answer::class);
    }

    public function checklist(): EmbedsMany
    {
        return $this->embedsMany(ChecklistItem::class);
    }

    public function to_public_output(): array
    {
        $output = [
            'application_id' => $this->_id,
            'status' => $this->status,
            'answers' => $this->answers->mapWithKeys(function (Answer $answer) {
                return [$answer->question->_id => $answer->answer];
            }),
            'checklist' => $this->checklist->map(function($item) {
                return $item->to_public_output();
            })
        ];

        return $output;
    }
}
