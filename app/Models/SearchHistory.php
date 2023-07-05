<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SearchHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'text_search',
        'user_id',
        'guide_id',
        'region_id',
    ];
    /**
     * Get the user that owns the SearchHistory
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function guide()
    {
        return $this->belongsTo(Guide::class);
    }
    public function region()
    {
        return $this->belongsTo(Region::class);
    }
}
