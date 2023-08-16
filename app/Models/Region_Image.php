<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Region_Image extends Model
{
    use HasFactory;

    protected $fillable = [
        'region_id',
        'url',

    ];

    public function region() : BelongsTo
    {
        return $this->belongsTo(Region::class);
    }
}
