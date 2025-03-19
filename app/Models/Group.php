<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'privacy', 'admin_id'];

    public function members()
    {
        return $this->hasMany(GroupMember::class);
    }

    public function posts()
    {
        return $this->hasMany(GroupPost::class);
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function posts()
    {
    return $this->hasMany(PostInGroup::class);
    }

}

