<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TradingOrder;
use App\Models\TradingPosition;
use Illuminate\Http\Request;

class TradingOrderController extends Controller
{
    public function index(Request $request)
    {
        $orders = TradingOrder::query()
            ->with('user:id,firstname,lastname,username')
            ->when($request->filled('symbol'), function ($query) use ($request) {
                $query->where('symbol', 'like', '%' . strtoupper(trim($request->symbol)) . '%');
            })
            ->when($request->filled('side') && in_array($request->side, ['buy', 'sell'], true), function ($query) use ($request) {
                $query->where('side', $request->side);
            })
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('broker_status', $request->status);
            })
            ->when($request->filled('trx_id'), function ($query) use ($request) {
                $query->where('trx_id', $request->trx_id);
            })
            ->latest()
            ->paginate(30)
            ->withQueryString();

        return view('admin.trading.orders.index', compact('orders'));
    }

    public function summary(Request $request)
    {
        return response()->json([
            'total_orders' => TradingOrder::query()->count(),
            'open_positions' => TradingPosition::query()->count(),
            'live_orders' => TradingOrder::query()->where('is_live_order', 1)->count(),
            'buy_volume' => (float) TradingOrder::query()->where('side', 'buy')->sum('gross_amount'),
            'sell_volume' => (float) TradingOrder::query()->where('side', 'sell')->sum('gross_amount'),
            'fees_collected' => (float) TradingOrder::query()->sum('fee_amount'),
            'updated_at' => now()->toDateTimeString(),
        ]);
    }
}

