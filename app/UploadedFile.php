<?php

namespace App;

use function foo\func;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;
use Jenssegers\Mongodb\Relations\BelongsTo;
use Jenssegers\Mongodb\Relations\BelongsToMany;
use Jenssegers\Mongodb\Relations\EmbedsMany;
use Jenssegers\Mongodb\Relations\EmbedsOne;
use Jenssegers\Mongodb\Relations\HasMany;

/**
 * Class UploadedFile
 * @package App
 * @property string file_name
 * @property string original_file_name
 * @property int size
 */
class UploadedFile extends Eloquent
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'file_name',
        'original_file_name',
        'size'
    ];

    public function applications(): BelongsToMany
    {
        return $this->belongsToMany(Application::class);
    }

    public function to_public_output(): array
    {
        $output = [
            'file_name' => $this->file_name,
            'original_file_name' => $this->original_file_name,
            'size' => $this->size
        ];

        return $output;
    }
}
