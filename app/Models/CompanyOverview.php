<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyOverview extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'description', 'website_link', 'phone_number', 'language', 'number_of_employees'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
