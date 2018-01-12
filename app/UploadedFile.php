<?php

namespace App;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

/**
 * Class UploadedFile
 * @package App
 * @property string file_name
 * @property string original_file_name
 * @property string mime_type
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
        'size',
        'mime_type'
    ];

    public function to_public_output(): array
    {
        $output = [
            'uploaded_file_id' => $this->_id,
            'file_name' => $this->file_name,
            'original_file_name' => $this->original_file_name,
            'size' => $this->size,
            'mime_type' => $this->mime_type
        ];

        return $output;
    }
}
