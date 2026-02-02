<?php

namespace App\Http\Controllers\Api;

use App\Models\Penalty;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class PenaltyController extends Controller
{
    public function index()
    {
        $penalties = Penalty::with('rental.user')->get();

        return response()->json([
            'success' => true,
            'message' => 'List of penalties',
            'data' => $penalties
        ], 200);
    }

    public function show($id)
    {
        $penalty = Penalty::with('rental.user')->find($id);

        if (!$penalty) {
            return response()->json([
                'success' => false,
                'message' => 'Penalty not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Penalty detail',
            'data' => $penalty
        ], 200);
    }


    public function markAsPaid($id)
    {
        $penalty = Penalty::find($id);

        if (!$penalty) {
            return response()->json([
                'success' => false,
                'message' => 'Penalty not found'
            ], 404);
        }

        if ($penalty->is_paid) {
            return response()->json([
                'success' => false,
                'message' => 'Penalty is already paid'
            ], 400);
        }

        $penalty->markAsPaid();

        return response()->json([
            'success' => true,
            'message' => 'Penalty marked as paid',
            'data' => $penalty
        ], 200);
    }

    public function unpaid()
    {
        $penalties = Penalty::with('rental.user')
            ->where('is_paid', false)
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Unpaid penalties',
            'data' => $penalties
        ], 200);
    }

    public function myPenalties()
    {
        $penalties = Penalty::with('rental.car')
            ->whereHas('rental', function($query) {
                $query->where('user_id', auth()->id());
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Your penalties',
            'data' => $penalties
        ], 200);
    }

    public function destroy($id)
    {
        $penalty = Penalty::find($id);

        if (!$penalty) {
            return response()->json([
                'success' => false,
                'message' => 'Penalty not found'
            ], 404);
        }

        $penalty->delete();

        return response()->json([
            'success' => true,
            'message' => 'Penalty deleted successfully'
        ], 200);
    }
}