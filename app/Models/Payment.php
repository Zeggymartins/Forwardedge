<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

     protected $fillable = [
        'user_id',
        'payable_id',
        'payable_type',
        'amount',
        'status',
        'method',
        'reference',
        'currency',
        'metadata'
    ];

    protected $casts = [
        'metadata' => 'array',
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Polymorphic relation (links to Enrollment, Order, or EventRegistration)
    public function payable()
    {
        return $this->morphTo();
    }
}
