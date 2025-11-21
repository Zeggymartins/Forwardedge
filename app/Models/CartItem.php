<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    protected $fillable = ['user_id', 'course_id', 'course_content_id', 'price', 'quantity'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function courseContent()
    {
        return $this->belongsTo(CourseContent::class);
    }

    public function payments()
    {
        return $this->morphMany(Payment::class, 'payable');
    }
}
