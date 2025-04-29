<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'profile_image',
        'phone',
        'role_id',
        'google_id',
        'facebook_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function Properties(): HasMany
    {
        return $this->hasMany(Property::class, 'Dealer_id');
    }
  
    public function Book_Preview():HasMany{
        return $this->HasMany(Property::class);
    }
    public function Favourite_Property():BelongsToMany
    {
        return $this->belongsToMany(Property::class, 'faivorate_property', 'user_id', 'property_id');

       
    }
    public function Orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
    public function Role():BelongsTo
    {
        return $this->belongsTo(Role::class,'role_id'); 
    }
    //Relation With Comment
    public function Comment():HasMany
    {
         return $this->HasMany(Comment::class);
    }
     //Relation With Rate
    public function Rate():HasMany
    {
         return $this->HasMany(Rate::class);
    }
    public function Request():HasOne
    {
         return $this->hasOne(request_trader::class,'id');
    }
 


}
