<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Guide_Rates extends Model
{
    use HasFactory;

    protected $table= "guide_rates";

    protected $fillable = [
        'rate',
        'user_id',
        'guide_id',
    ];

    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function guide() : BelongsTo
    {
        return $this->belongsTo(Guide::class);
    }
}
