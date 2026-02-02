<?php

namespace App\Http\Controllers\Api;

use App\Models\Car;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class CarController extends Controller
{
    public function index()
    {
        $cars = Car::all();

        return response()->json([
            'success' => true,
            'message' => 'List of cars',
            'data' => $cars
        ], 200);
    }

    public function available()
    {
        $cars = Car::where('status', 'available')->get();

        return response()->json([
            'success' => true,
            'message' => 'Available cars',
            'data' => $cars
        ], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'brand' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'plate_number' => 'required|string|max:20|unique:cars',
            'year' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'price_per_day' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $car = Car::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Car created successfully',
            'data' => $car
        ], 201);
    }

    public function show($id)
    {
        $car = Car::find($id);

        if (!$car) {
            return response()->json([
                'success' => false,
                'message' => 'Car not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Car detail',
            'data' => $car
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $car = Car::find($id);

        if (!$car) {
            return response()->json([
                'success' => false,
                'message' => 'Car not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'brand' => 'sometimes|string|max:255',
            'model' => 'sometimes|string|max:255',
            'plate_number' => 'sometimes|string|max:20|unique:cars,plate_number,' . $id,
            'year' => 'sometimes|integer|min:1900|max:' . (date('Y') + 1),
            'price_per_day' => 'sometimes|numeric|min:0',
            'status' => 'sometimes|in:available,rented',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $car->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Car updated successfully',
            'data' => $car
        ], 200);
    }

    public function destroy($id)
    {
        $car = Car::find($id);

        if (!$car) {
            return response()->json([
                'success' => false,
                'message' => 'Car not found'
            ], 404);
        }

        if ($car->status === 'rented') {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete car that is currently rented'
            ], 400);
        }

        $car->delete();

        return response()->json([
            'success' => true,
            'message' => 'Car deleted successfully'
        ], 200);
    }
}