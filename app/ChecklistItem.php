<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;
use Jenssegers\Mongodb\Relations\BelongsTo;
use Jenssegers\Mongodb\Relations\EmbedsMany;

/**
 * Class Application
 * @package App
 * @property string status
 * @property string title
 */
class ChecklistItem extends Eloquent
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'status',
        'title'
    ];

    public function to_public_output(): array
    {
        $output = [
            'status' => $this->status,
            'title' => $this->title
        ];

        return $output;
    }
}