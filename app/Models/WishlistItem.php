<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WishlistItem extends Model
{
    protected $fillable = ['user_id', 'course_id', 'price'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }
}
