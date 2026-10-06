<?php

use App\Livewire\Admin\InventoryIndex;
use App\Models\Category;
use App\Models\SneakerVariant;
use App\Models\StockMovement;
use App\Models\User;
use Database\Seeders\SneakerInventorySeeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;

test('admin can create a shoe variant with its opening stock movement', function () {
    $user = User::factory()->create();
    $user->assignRole(Role::create(['name' => 'admin']));
    $category = Category::factory()->create();

    Livewire::actingAs($user)->test(InventoryIndex::class)
        ->call('openCreate')
        ->set('brand', 'Nike')
        ->set('modelName', 'Air Max 90')
        ->set('sku', 'nk-am90-blk-27')
        ->set('color', 'Negro / blanco')
        ->set('size', '27')
        ->set('categoryId', (string) $category->id)
        ->set('salePrice', '500')
        ->set('initialStock', 8)
        ->call('saveVariant')
        ->assertHasNoErrors()
        ->assertSet('showForm', false);

    $variant = SneakerVariant::query()->firstOrFail();

    expect($variant->sku)->toBe('NK-AM90-BLK-27')
        ->and((float) $variant->sale_price)->toBe(500.0)
        ->and($variant->stock)->toBe(8)
        ->and($variant->category_id)->toBe($category->id)
        ->and((float) $variant->cost_price)->toBe(0.0)
        ->and($variant->min_stock)->toBe(3);

    $this->assertDatabaseHas('stock_movements', [
        'sneaker_variant_id' => $variant->id,
        'type' => 'in',
        'quantity_change' => 8,
        'note' => 'Stock inicial',
    ]);
});

test('admin can upload, preview, display, and replace a variant image', function () {
    Storage::fake('public');
    $user = User::factory()->create();
    $user->assignRole(Role::create(['name' => 'admin']));
    $imageContent = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+a5WQAAAAASUVORK5CYII=');

    $component = Livewire::actingAs($user)->test(InventoryIndex::class)
        ->call('openCreate')
        ->set('brand', 'Nike')
        ->set('modelName', 'Air Max 90')
        ->set('sku', 'NK-AM90-IMAGE-27')
        ->set('color', 'Negro')
        ->set('size', '27')
        ->set('salePrice', '450')
        ->set('initialStock', 3)
        ->set('image', UploadedFile::fake()->createWithContent('air-max.png', $imageContent))
        ->assertSee('Vista previa de la imagen del tenis')
        ->call('saveVariant')
        ->assertHasNoErrors()
        ->assertSee('sneaker-product__image');

    $variant = SneakerVariant::query()->where('sku', 'NK-AM90-IMAGE-27')->firstOrFail();
    $oldImagePath = $variant->image_path;

    expect($oldImagePath)->not->toBeNull();
    Storage::disk('public')->assertExists($oldImagePath);
    $this->actingAs($user)->get(route('admin.inventory.image', $variant))->assertOk();

    $component->call('openEdit', $variant->id)
        ->assertSee('Imagen actual del tenis')
        ->set('image', UploadedFile::fake()->createWithContent('air-max-new.png', $imageContent))
        ->call('saveVariant')
        ->assertHasNoErrors();

    $newImagePath = $variant->fresh()->image_path;
    expect($newImagePath)->not->toBe($oldImagePath);
    Storage::disk('public')->assertExists($newImagePath);
    Storage::disk('public')->assertMissing($oldImagePath);
});

test('admin cannot set a pair price above 500 bolivianos', function () {
    $user = User::factory()->create();
    $user->assignRole(Role::create(['name' => 'admin']));
    $category = Category::factory()->create();

    Livewire::actingAs($user)->test(InventoryIndex::class)
        ->call('openCreate')
        ->set('brand', 'Nike')
        ->set('modelName', 'Air Max 90')
        ->set('sku', 'NK-AM90-OVER-27')
        ->set('color', 'Negro')
        ->set('size', '27')
        ->set('categoryId', (string) $category->id)
        ->set('salePrice', '500.01')
        ->set('initialStock', 1)
        ->call('saveVariant')
        ->assertHasErrors('salePrice')
        ->assertSee('El precio por par no puede superar Bs 500.');

    expect(SneakerVariant::query()->count())->toBe(0);
});

test('stock adjustments are recorded and cannot reduce stock below zero', function () {
    $user = User::factory()->create();
    $user->assignRole(Role::create(['name' => 'admin']));
    $variant = SneakerVariant::query()->create([
        'brand' => 'Adidas',
        'model' => 'Campus 00s',
        'sku' => 'AD-CAMPUS-GRN-26',
        'color' => 'Verde',
        'size' => '26',
        'cost_price' => 300,
        'sale_price' => 450,
        'stock' => 5,
        'min_stock' => 2,
    ]);

    $component = Livewire::actingAs($user)->test(InventoryIndex::class)
        ->call('openAdjustment', $variant->id)
        ->set('adjustmentType', 'out')
        ->set('adjustmentQuantity', 6)
        ->call('adjustStock')
        ->assertHasErrors('adjustmentQuantity');

    expect($variant->fresh()->stock)->toBe(5)
        ->and(StockMovement::query()->count())->toBe(0);

    $component->set('adjustmentQuantity', 2)
        ->set('adjustmentNote', 'Venta en tienda')
        ->call('adjustStock')
        ->assertHasNoErrors();

    expect($variant->fresh()->stock)->toBe(3);
    $this->assertDatabaseHas('stock_movements', [
        'sneaker_variant_id' => $variant->id,
        'type' => 'out',
        'quantity_change' => -2,
        'note' => 'Venta en tienda',
    ]);
});

test('sales of one pair, half a dozen, and a dozen deduct the correct pairs', function () {
    $user = User::factory()->create();
    $user->assignRole(Role::create(['name' => 'admin']));
    $variant = SneakerVariant::query()->create([
        'brand' => 'Nike',
        'model' => 'Air Max 90',
        'sku' => 'NK-AM90-SALE-27',
        'color' => 'Negro',
        'size' => '27',
        'sale_price' => 450,
        'stock' => 20,
        'min_stock' => 2,
    ]);

    $component = Livewire::actingAs($user)->test(InventoryIndex::class);

    foreach ([1, 6, 12] as $pairs) {
        $component->call('openAdjustment', $variant->id)
            ->set('adjustmentType', 'out')
            ->set('adjustmentQuantity', $pairs)
            ->set('adjustmentNote', 'Venta de prueba')
            ->call('adjustStock')
            ->assertHasNoErrors();
    }

    expect($variant->fresh()->stock)->toBe(1)
        ->and($variant->movements()->pluck('quantity_change')->all())->toBe([-1, -6, -12]);

    $component->call('openAdjustment', $variant->id)
        ->set('adjustmentType', 'out')
        ->set('adjustmentQuantity', 12)
        ->call('adjustStock')
        ->assertHasErrors('adjustmentQuantity');

    expect($variant->fresh()->stock)->toBe(1)
        ->and($variant->movements()->count())->toBe(3);
});

test('inventory search filters variants by model and sku', function () {
    $user = User::factory()->create();
    $user->assignRole(Role::create(['name' => 'admin']));

    SneakerVariant::query()->create([
        'brand' => 'Nike',
        'model' => 'Air Force 1',
        'sku' => 'NK-AF1-WHT-25',
        'color' => 'Blanco',
        'size' => '25',
        'cost_price' => 300,
        'sale_price' => 450,
        'stock' => 3,
        'min_stock' => 1,
    ]);
    SneakerVariant::query()->create([
        'brand' => 'Puma',
        'model' => 'Suede Classic',
        'sku' => 'PM-SUEDE-BLK-26',
        'color' => 'Negro',
        'size' => '26',
        'cost_price' => 280,
        'sale_price' => 420,
        'stock' => 2,
        'min_stock' => 1,
    ]);

    Livewire::actingAs($user)->test(InventoryIndex::class)
        ->set('search', 'AF1')
        ->assertSee('Air Force 1')
        ->assertDontSee('Suede Classic');
});

test('regular users cannot initialize the inventory component', function () {
    $user = User::factory()->create();
    $user->assignRole(Role::create(['name' => 'user']));

    Livewire::actingAs($user)
        ->test(InventoryIndex::class)
        ->assertForbidden();
});

test('dashboard renders current inventory indicators', function () {
    SneakerVariant::query()->create([
        'brand' => 'New Balance',
        'model' => '574',
        'sku' => 'NB-574-GRY-27',
        'color' => 'Gris',
        'size' => '27',
        'cost_price' => 315,
        'sale_price' => 480,
        'stock' => 4,
        'min_stock' => 2,
    ]);

    $this->actingAs(User::factory()->create())
        ->get(route('dashboard'))
        ->assertOk()
        ->assertSee('Pares en tienda')
        ->assertSee('4');
});

test('sample inventory seeding is repeatable without duplicating variants or movements', function () {
    $this->seed(SneakerInventorySeeder::class);
    $variantCount = SneakerVariant::query()->count();
    $movementCount = StockMovement::query()->count();

    $this->seed(SneakerInventorySeeder::class);

    expect($variantCount)->toBe(8)
        ->and(SneakerVariant::query()->count())->toBe($variantCount)
        ->and(StockMovement::query()->count())->toBe($movementCount)
        ->and(SneakerVariant::query()->sum('stock'))->toBe(29)
        ->and(Category::query()->whereIn('name', ['Running', 'Casual', 'Basquetbol', 'Skate'])->count())->toBe(4);
});
