<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\Tag;

class Post extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'content', 'media', 'privacy'];

    protected $casts = [
        'media' => 'array', // Ensures the media field is stored as an array
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function likes(): HasMany
    {
        return $this->hasMany(PostLike::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(PostComment::class);
    }

    

    public function tags() {
        return $this->hasMany(Tag::class);
    }
}

