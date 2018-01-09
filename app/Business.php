<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;
use Jenssegers\Mongodb\Relations\BelongsTo;

/**
 * Class Business
 * @package App
 * @property string name
 * @property string description
 * @property string product
 */
class Business extends Eloquent
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'product'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo('User');
    }

    public function to_auth_output(): array
    {
        $output = [
            'name' => $this->name,
            'description' => $this->description,
            'product' => $this->product
        ];

        return $output;
    }
}
