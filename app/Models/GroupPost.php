<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GroupPost extends Model
{
    use HasFactory;

    protected $fillable = ['group_id', 'post_id'];

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function post()
    {
        return $this->belongsTo(Post::class);
    }
}
