<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\Car;
use App\Models\Rental;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class RentalController extends Controller
{
    public function index()
    {
        $rentals = Rental::with(['user', 'car'])->get();

        return response()->json([
            'success' => true,
            'message' => 'List of rentals',
            'data' => $rentals
        ], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'car_id' => 'required|exists:cars,id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after:start_date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $car = Car::find($request->car_id);
        
        if ($car->status !== 'available') {
            return response()->json([
                'success' => false,
                'message' => 'Car is not available for rent'
            ], 400);
        }

        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);
        $totalDays = $startDate->diffInDays($endDate) + 1;

        $totalPrice = $totalDays * $car->price_per_day;

        $rental = Rental::create([
            'user_id' => auth()->id(),
            'car_id' => $request->car_id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'total_days' => $totalDays,
            'total_price' => $totalPrice,
            'status' => 'pending',
        ]);

        $car->update(['status' => 'rented']);

        return response()->json([
            'success' => true,
            'message' => 'Rental successfully',
            'data' => $rental->load(['user', 'car'])
        ], 201);
    }

    public function show($id)
    {
        $rental = Rental::with(['user', 'car'])->find($id);

        if (!$rental) {
            return response()->json([
                'success' => false,
                'message' => 'Rental not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Rental detail',
            'data' => $rental
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $rental = Rental::find($id);

        if (!$rental) {
            return response()->json([
                'success' => false,
                'message' => 'Rental not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'status' => 'required|in:pending,active,completed,cancelled',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $rental->update([
            'status' => $request->status
        ]);

        if ($request->status === 'cancelled') {
            $rental->car->update(['status' => 'available']);
        }

        if ($request->status === 'active') {
            $rental->car->update(['status' => 'rented']);
        }

        return response()->json([
            'success' => true,
            'message' => 'Rental updated successfully',
            'data' => $rental->load(['user', 'car'])
        ], 200);
    }

    public function destroy($id)
    {
        $rental = Rental::find($id);

        if (!$rental) {
            return response()->json([
                'success' => false,
                'message' => 'Rental not found'
            ], 404);
        }

        $rental->car->update(['status' => 'available']);

        $rental->delete();

        return response()->json([
            'success' => true,
            'message' => 'Rental deleted successfully'
        ], 200);
    }

    public function myRentals()
    {
        $rentals = Rental::with(['car'])
            ->where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Your rental history',
            'data' => $rentals
        ], 200);
    }
}