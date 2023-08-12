<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */


   protected $table = "users";

    protected $fillable = [
        'name',
        'email',
        'password',
        'number',
        'image',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

      /**
       * Get all of the comments for the User
       *
       * @return \Illuminate\Database\Eloquent\Relations\HasMany
       */
      public function comments(): HasMany
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
      public function searchs() : HasMany
      {
          return $this->hasMany(SearchHistory::class);
      }
      public function guide_rates() : HasMany
      {
        return $this->hasMany(Guide_Rates::class);
      }

    }

