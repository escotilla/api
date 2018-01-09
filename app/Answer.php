<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;
use Jenssegers\Mongodb\Relations\BelongsTo;
use Jenssegers\Mongodb\Relations\EmbedsOne;
use Jenssegers\Mongodb\Relations\HasOne;

/**
 * Class Answer
 * @package App
 * @property string short_question
 * @property string english
 * @property string spanish
 * @property string category
 * @property string answer_type
 */
class Answer extends Eloquent
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'answer'
    ];

    public function question(): EmbedsOne
    {
        return $this->embedsOne(Question::class);
    }

    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }

    public function to_public_output(): array
    {
        $output = $this->toArray();

        return $output;
    }
}
