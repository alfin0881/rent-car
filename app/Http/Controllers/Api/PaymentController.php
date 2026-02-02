<?php

namespace App\Http\Controllers\Api;

use App\Models\Rental;
use App\Models\Payment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class PaymentController extends Controller
{
    public function index()
    {
        $payments = Payment::with('rental.user')->get();

        return response()->json([
            'success' => true,
            'message' => 'List of payments',
            'data' => $payments
        ], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'rental_id' => 'required|exists:rentals,id',
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|in:cash,transfer,credit_card,debit_card',
            'payment_date' => 'required|date',
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
                'message' => 'Unauthorized to create payment for this rental'
            ], 403);
        }

        if ($rental->payment) {
            return response()->json([
                'success' => false,
                'message' => 'Payment already exists for this rental'
            ], 400);
        }

        $payment = Payment::create([
            'rental_id' => $request->rental_id,
            'amount' => $request->amount,
            'payment_method' => $request->payment_method,
            'payment_date' => $request->payment_date,
            'status' => 'pending',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Payment created successfully',
            'data' => $payment->load('rental')
        ], 201);
    }

    public function show($id)
    {
        $payment = Payment::with('rental.user')->find($id);

        if (!$payment) {
            return response()->json([
                'success' => false,
                'message' => 'Payment not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Payment detail',
            'data' => $payment
        ], 200);
    }

    public function updateStatus(Request $request, $id)
    {
        $payment = Payment::find($id);

        if (!$payment) {
            return response()->json([
                'success' => false,
                'message' => 'Payment not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'status' => 'required|in:pending,completed,failed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $payment->update(['status' => $request->status]);

        if ($request->status === 'completed') {
            $payment->rental->update(['status' => 'active']);
        }

        return response()->json([
            'success' => true,
            'message' => 'Payment status updated successfully',
            'data' => $payment->load('rental')
        ], 200);
    }

    public function destroy($id)
    {
        $payment = Payment::find($id);

        if (!$payment) {
            return response()->json([
                'success' => false,
                'message' => 'Payment not found'
            ], 404);
        }

        $payment->delete();

        return response()->json([
            'success' => true,
            'message' => 'Payment deleted successfully'
        ], 200);
    }

    public function myPayments()
    {
        $payments = Payment::with('rental.car')
            ->whereHas('rental', function($query) {
                $query->where('user_id', auth()->id());
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Your payment history',
            'data' => $payments
        ], 200);
    }
}