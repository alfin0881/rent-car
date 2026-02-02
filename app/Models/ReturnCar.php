<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class ReturnCar extends Model
{
    use HasFactory;

    protected $table = 'returns';

    protected $fillable = [
        'rental_id',
        'return_date',
        'late_days',
        'penalty_id',
    ];

    protected $casts = [
        'return_date' => 'date',
    ];

    public function rental()
    {
        return $this->belongsTo(Rental::class);
    }


    public function penalty()
    {
        return $this->belongsTo(Penalty::class);
    }

    public static function calculateLateDays($endDate, $returnDate)
    {
        $end = Carbon::parse($endDate);
        $return = Carbon::parse($returnDate);
        
        $lateDays = $return->diffInDays($end);
        
        return $return->gt($end) ? $lateDays : 0;
    }
}