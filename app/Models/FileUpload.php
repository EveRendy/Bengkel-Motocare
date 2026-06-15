<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class FileUpload extends Model
{
    protected $fillable = [
        'fileable_type',
        'fileable_id',
        'path',
        'original_name',
        'mime_type',
        'size',
    ];

    public function fileable(): MorphTo
    {
        return $this->morphTo();
    }
}