<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    
    protected $fillable = [
        'first_name', 'last_name', 'username', 'description', 'date_of_birth',
        'language', 'location', 'relationship_status',
        'profile_picture', 'banner_picture',
        'email', 'password' 
    ];

    

    // Relationships
    public function workExperiences()
    {
        return $this->hasMany(WorkExperience::class);
    }

    public function educations()
    {
        return $this->hasMany(Education::class, 'user_id');
    }

    public function skills()
    {
        return $this->hasMany(Skill::class);
    }

    public function companyOverview()
    {
        return $this->hasOne(CompanyOverview::class);
    }

    // Post
    public function posts()
    {
    return $this->hasMany(Post::class);
    }

    public function likedPosts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class, 'post_likes')->withTimestamps();
    }

    public function comments(): HasMany
    {
        return $this->hasMany(PostComment::class);
    }

    public function groupPosts()
    {
    return $this->hasMany(PostInGroup::class);
    }


}
