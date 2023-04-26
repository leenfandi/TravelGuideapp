<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class region extends Model
{
    use HasFactory;
    protected $table = "regions";
    protected $fillable = [
        'name',
        'lon',
        'lat',
        'weather',
        'user_id'

    ];

        /**
         * Get all of the user for the region
         *
         * @return \Illuminate\Database\Eloquent\Relations\HasMany
         */
        public function users(): HasMany
        {
            return $this->hasMany(User::class);
        }
    }

