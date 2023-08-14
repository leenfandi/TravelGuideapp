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
        'latitude' ,
        'longitude' ,
        'admin_id' ,
        'guide_id'

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
    public function rates() : HasMany
    {
        return $this->hasMany(Rate::class);
    }

    public function bookmarks() : HasMany
    {
        return $this->hasMany(Bookmark::class);
    }
    public function admin() : BelongsTo
    {
        return $this->belongsTo(Admin::class);
    }
    public function guide() : BelongsTo
    {
        return $this->belongsTo(Guide::class);
    }
}
