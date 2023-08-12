<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Region extends Model
{
    use HasFactory;
    protected $table = "regions";
    protected $fillable = [
        'city_id' ,
        'name',


    ];

        /**
         * Get all of the user for the region
         *
         * @return \Illuminate\Database\Eloquent\Relations\HasMany
         */


         public function city() : BelongsTo
         {
             return $this->belongsTo(City::class);
         }

         public function activities() : HasMany
         {
             return $this->hasMany(Activity::class);
         }
         public function searchs()
        {
            return $this->hasMany(SearchHistory::class);
        }
    }

