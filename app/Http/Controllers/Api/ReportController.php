<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\Car;
use App\Models\User;
use App\Models\Rental;
use App\Models\Payment;
use App\Models\Penalty;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class ReportController extends Controller
{
    public function rentalReport(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth());
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth());

        $rentals = Rental::with(['user', 'car'])
            ->whereBetween('start_date', [$startDate, $endDate])
            ->get();

        $summary = [
            'total_rentals' => $rentals->count(),
            'total_revenue' => $rentals->sum('total_price'),
            'pending' => $rentals->where('status', 'pending')->count(),
            'active' => $rentals->where('status', 'active')->count(),
            'completed' => $rentals->where('status', 'completed')->count(),
            'cancelled' => $rentals->where('status', 'cancelled')->count(),
        ];

        return response()->json([
            'success' => true,
            'message' => 'Rental report',
            'data' => [
                'period' => [
                    'start_date' => $startDate,
                    'end_date' => $endDate
                ],
                'summary' => $summary,
                'rentals' => $rentals
            ]
        ], 200);
    }

    public function paymentReport(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth());
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth());

        $payments = Payment::with('rental')
            ->whereBetween('payment_date', [$startDate, $endDate])
            ->get();

        $summary = [
            'total_payments' => $payments->count(),
            'total_amount' => $payments->where('status', 'completed')->sum('amount'),
            'pending' => $payments->where('status', 'pending')->sum('amount'),
            'completed' => $payments->where('status', 'completed')->sum('amount'),
            'failed' => $payments->where('status', 'failed')->count(),
            'by_method' => [
                'cash' => $payments->where('payment_method', 'cash')->sum('amount'),
                'transfer' => $payments->where('payment_method', 'transfer')->sum('amount'),
                'credit_card' => $payments->where('payment_method', 'credit_card')->sum('amount'),
                'debit_card' => $payments->where('payment_method', 'debit_card')->sum('amount'),
            ]
        ];

        return response()->json([
            'success' => true,
            'message' => 'Payment report',
            'data' => [
                'period' => [
                    'start_date' => $startDate,
                    'end_date' => $endDate
                ],
                'summary' => $summary,
                'payments' => $payments
            ]
        ], 200);
    }

    public function penaltyReport()
    {
        $penalties = Penalty::with('rental.user')->get();

        $summary = [
            'total_penalties' => $penalties->count(),
            'total_amount' => $penalties->sum('amount'),
            'paid' => $penalties->where('is_paid', true)->sum('amount'),
            'unpaid' => $penalties->where('is_paid', false)->sum('amount'),
            'unpaid_count' => $penalties->where('is_paid', false)->count(),
        ];

        return response()->json([
            'success' => true,
            'message' => 'Penalty report',
            'data' => [
                'summary' => $summary,
                'penalties' => $penalties
            ]
        ], 200);
    }


    public function mostRentedCars()
    {
        $cars = Car::withCount(['rentals' => function($query) {
            $query->where('status', 'completed');
        }])
        ->orderBy('rentals_count', 'desc')
        ->take(10)
        ->get();

        return response()->json([
            'success' => true,
            'message' => 'Most rented cars',
            'data' => $cars
        ], 200);
    }

    public function dashboard()
    {
        $today = Carbon::today();
        $thisMonth = Carbon::now()->startOfMonth();

        $stats = [
            'total_cars' => Car::count(),
            'available_cars' => Car::where('status', 'available')->count(),
            'rented_cars' => Car::where('status', 'rented')->count(),
            'total_users' => User::where('role_id', '!=', 1)->count(),
            'active_rentals' => Rental::where('status', 'active')->count(),
            'pending_rentals' => Rental::where('status', 'pending')->count(),
            'today_rentals' => Rental::whereDate('start_date', $today)->count(),
            'month_revenue' => Rental::where('status', 'completed')
                ->where('created_at', '>=', $thisMonth)
                ->sum('total_price'),
            'unpaid_penalties' => Penalty::where('is_paid', false)->sum('amount'),
            'pending_payments' => Payment::where('status', 'pending')->sum('amount'),
        ];

        $recentRentals = Rental::with(['user', 'car'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Dashboard statistics',
            'data' => [
                'statistics' => $stats,
                'recent_rentals' => $recentRentals
            ]
        ], 200);
    }
}