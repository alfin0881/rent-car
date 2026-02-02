<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\Rental;
use App\Models\Penalty;
use App\Models\ReturnCar;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class ReturnController extends Controller
{
    public function index()
    {
        $returns = ReturnCar::with(['rental.user', 'rental.car', 'penalty'])->get();

        return response()->json([
            'success' => true,
            'message' => 'List of returns',
            'data' => $returns
        ], 200);
    }
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'rental_id' => 'required|exists:rentals,id',
            'return_date' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $rental = Rental::find($request->rental_id);

        if (!auth()->user()->isAdmin() && $rental->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to return this rental'
            ], 403);
        }

        if ($rental->status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'Rental is not active. Cannot process return.'
            ], 400);
        }

        if ($rental->returnRecord) {
            return response()->json([
                'success' => false,
                'message' => 'This rental has already been returned'
            ], 400);
        }

        $lateDays = ReturnCar::calculateLateDays($rental->end_date, $request->return_date);

        $penaltyId = null;
        
        if ($lateDays > 0) {
            $penaltyAmount = Penalty::calculateAmount($lateDays);
            
            $penalty = Penalty::create([
                'rental_id' => $rental->id,
                'amount' => $penaltyAmount,
                'reason' => "Late return: {$lateDays} day(s) x Rp 50,000",
                'is_paid' => false,
            ]);

            $penaltyId = $penalty->id;
        }

        $return = ReturnCar::create([
            'rental_id' => $request->rental_id,
            'return_date' => $request->return_date,
            'late_days' => $lateDays,
            'penalty_id' => $penaltyId,
        ]);

        $rental->update(['status' => 'completed']);

        $rental->car->markAsAvailable();

        return response()->json([
            'success' => true,
            'message' => 'Car returned successfully',
            'data' => $return->load(['rental.car', 'penalty'])
        ], 201);
    }

    public function show($id)
    {
        $return = ReturnCar::with(['rental.user', 'rental.car', 'penalty'])->find($id);

        if (!$return) {
            return response()->json([
                'success' => false,
                'message' => 'Return record not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Return detail',
            'data' => $return
        ], 200);
    }

    public function destroy($id)
    {
        $return = ReturnCar::find($id);

        if (!$return) {
            return response()->json([
                'success' => false,
                'message' => 'Return record not found'
            ], 404);
        }

        if ($return->penalty) {
            $return->penalty->delete();
        }

        $return->delete();

        return response()->json([
            'success' => true,
            'message' => 'Return record deleted successfully'
        ], 200);
    }

    public function myReturns()
    {
        $returns = ReturnCar::with(['rental.car', 'penalty'])
            ->whereHas('rental', function($query) {
                $query->where('user_id', auth()->id());
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Your return history',
            'data' => $returns
        ], 200);
    }
}