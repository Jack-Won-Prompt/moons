<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Inventory;
use App\Models\StockTransfer;

class InventoryController extends Controller
{
    public function index()
    {
        $stats = [
            'skus'      => Inventory::distinct('product_id')->count('product_id'),
            'units'     => (int) Inventory::sum('quantity'),
            'stores'    => Inventory::distinct('store_id')->count('store_id'),
            'transfers' => StockTransfer::whereIn('status', ['requested', 'approved', 'shipping'])->count(),
        ];

        $byStore = Inventory::selectRaw('store_id, count(*) skus, sum(quantity) units')
            ->with('store')->groupBy('store_id')->get();

        $transfers = StockTransfer::with(['product', 'fromStore', 'toStore'])->latest()->paginate(15);

        return view('admin.inventory.index', compact('stats', 'byStore', 'transfers'));
    }
}
