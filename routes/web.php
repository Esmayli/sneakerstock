<?php

use App\Models\SneakerVariant;
use App\Models\StockMovement;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function () {
        $summary = SneakerVariant::query()
            ->selectRaw('COUNT(*) as variants, COALESCE(SUM(stock), 0) as units, COALESCE(SUM(stock * sale_price), 0) as retail_value')
            ->first();

        return view('dashboard', [
            'summary' => $summary,
            'lowStock' => SneakerVariant::lowStock()
                ->orderBy('stock')
                ->limit(5)
                ->get(),
            'recentMovements' => StockMovement::query()
                ->with('sneakerVariant')
                ->latest()
                ->limit(5)
                ->get(),
        ]);
    })->name('dashboard');
});

require __DIR__.'/settings.php';
