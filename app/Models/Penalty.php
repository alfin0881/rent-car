<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penalty extends Model
{
    use HasFactory;

    protected $fillable = [
        'rental_id',
        'amount',
        'reason',
        'is_paid',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'is_paid' => 'boolean',
    ];

    public function rental()
    {
        return $this->belongsTo(Rental::class);
    }

 
    public function returnRecord()
    {
        return $this->hasOne(ReturnCar::class);
    }

    public function markAsPaid()
    {
        $this->update(['is_paid' => true]);
    }


    public static function calculateAmount($lateDays, $dailyPenalty = 50000)
    {
        return $lateDays * $dailyPenalty;
    }
}