<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Activity extends Model
{
    use HasFactory;

    protected $fillable = [

        'region_id',
        'name',
        'type',
        'description',
        //'distance',
        //'time',
        'price',
    ];

    public function region() : BelongsTo
    {
        return $this->belongsTo(Region::class);
    }

    public function images() : HasMany
    {
        return $this->hasMany(Image::class);
    }
    public function comments()
    {
       return $this->hasMany(Comment::class);
    }

}
