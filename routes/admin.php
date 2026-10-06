<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\InventoryVariantImageController;
use App\Http\Controllers\Admin\UserController;
use App\Livewire\Admin\InventoryIndex;
use Illuminate\Support\Facades\Route;

Route::get('inventory', InventoryIndex::class)->name('inventory.index');
Route::get('inventory/{variant}/image', InventoryVariantImageController::class)->name('inventory.image');
Route::get('categories/{category}/image', [CategoryController::class, 'image'])->name('categories.image');
Route::resource('categories', CategoryController::class)->except('show');
Route::get('user', [UserController::class, 'index'])->name('users.index');
