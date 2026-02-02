<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Car extends Model
{
    use HasFactory;

    protected $fillable = [
        'brand',
        'model',
        'plate_number',
        'year',
        'price_per_day',
        'status',
    ];

    protected $casts = [
        'price_per_day' => 'decimal:2',
        'year' => 'integer',
    ];

    public function rentals()
    {
        return $this->hasMany(Rental::class);
    }


    public function isAvailable()
    {
        return $this->status === 'available';
    }

 
    public function markAsRented()
    {
        $this->update(['status' => 'rented']);
    }

    public function markAsAvailable()
    {
        $this->update(['status' => 'available']);
    }
}