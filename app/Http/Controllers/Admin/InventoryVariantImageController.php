<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SneakerVariant;
use Illuminate\Support\Facades\Storage;

class InventoryVariantImageController extends Controller
{
    public function __invoke(SneakerVariant $variant)
    {
        abort_unless($variant->image_path && Storage::disk('public')->exists($variant->image_path), 404);

        return Storage::disk('public')->response($variant->image_path);
    }
}
