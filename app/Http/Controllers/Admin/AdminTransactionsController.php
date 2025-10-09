<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Orders;
use App\Models\Payment;
use Illuminate\Http\Request;

class AdminTransactionsController extends Controller
{
    public function index()
    {
        $transactions = Payment::with(['user', 'payable'])
            ->latest()
            ->paginate(10);

        return view('admin.pages.transactions', compact('transactions'));
    }

    /**
     * View a single transaction (optional JSON).
     */
    public function show($id)
    {
        $transaction = Payment::with(['user', 'payable'])->findOrFail($id);
        return response()->json($transaction);
    }

    public function getOrders()
    {
        $orders = Orders::with(['user', 'items.course'])->latest()->paginate(10);
        return view('admin.pages.orders', compact('orders'));
    }
}
