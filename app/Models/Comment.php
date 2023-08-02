<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;
    protected $table= "comments";
    protected $fillable = [
        'message',
        'user_id',
        'activities_id',
        'guide_id',
    ];

    /**
     * Get the user that owns the Comment
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
/**
 * Get the user that owns the Comment
 *
 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
 */
public function guide()
{
    return $this->belongsTo(Guide::class);

}

/**
 * Get the user that owns the Comment
 *
 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
 */
public function activity()
{
    return $this->belongsTo(Activity::class);
}
}
