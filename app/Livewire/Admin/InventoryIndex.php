<?php

namespace App\Livewire\Admin;

use App\Models\Category;
use App\Models\SneakerVariant;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Throwable;

class InventoryIndex extends Component
{
    use WithFileUploads, WithPagination;

    public string $search = '';

    public string $stockFilter = 'all';

    public string $categoryFilter = '';

    public bool $showForm = false;

    public ?int $editingId = null;

    public string $brand = '';

    public string $modelName = '';

    public string $sku = '';

    public string $color = '';

    public string $size = '';

    public string $categoryId = '';

    public string $salePrice = '';

    public int $initialStock = 0;

    public $image = null;

    public ?string $existingImageUrl = null;

    public ?int $adjustingId = null;

    public string $adjustmentType = 'in';

    public int $adjustmentQuantity = 1;

    public string $adjustmentNote = '';

    public function boot(): void
    {
        abort_unless(auth()->user()?->hasRole('admin'), 403);
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStockFilter(): void
    {
        $this->resetPage();
    }

    public function updatedCategoryFilter(): void
    {
        $this->resetPage();
    }

    public function openCreate(): void
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function openEdit(int $id): void
    {
        $variant = SneakerVariant::findOrFail($id);

        $this->resetForm();
        $this->editingId = $variant->id;
        $this->brand = $variant->brand;
        $this->modelName = $variant->model;
        $this->sku = $variant->sku;
        $this->color = $variant->color;
        $this->size = $variant->size;
        $this->categoryId = (string) ($variant->category_id ?? '');
        $this->salePrice = (string) $variant->sale_price;
        $this->existingImageUrl = $variant->image_path
            ? route('admin.inventory.image', $variant)
            : null;
        $this->showForm = true;
    }

    public function saveVariant(): void
    {
        $rules = [
            'brand' => ['required', 'string', 'max:80'],
            'modelName' => ['required', 'string', 'max:120'],
            'sku' => ['required', 'string', 'max:60', Rule::unique('sneaker_variants', 'sku')->ignore($this->editingId)],
            'color' => ['required', 'string', 'max:60'],
            'size' => ['required', 'string', 'max:16'],
            'categoryId' => ['nullable', 'exists:categories,id'],
            'salePrice' => ['required', 'numeric', 'min:0', 'max:500'],
            'image' => ['nullable', 'file', 'mimetypes:image/jpeg,image/png,image/gif,image/bmp,image/webp,image/avif', 'max:51200'],
        ];

        if ($this->editingId === null) {
            $rules['initialStock'] = ['required', 'integer', 'min:0'];
        }

        $data = $this->validate($rules, [
            'salePrice.max' => 'El precio por par no puede superar Bs 500.',
        ]);
        $attributes = [
            'category_id' => $data['categoryId'] ?: null,
            'brand' => trim($data['brand']),
            'model' => trim($data['modelName']),
            'sku' => strtoupper(trim($data['sku'])),
            'color' => trim($data['color']),
            'size' => trim($data['size']),
            'sale_price' => $data['salePrice'],
        ];

        $imagePath = $this->image?->store('tenis', 'public');

        if ($this->image && ! $imagePath) {
            throw new \RuntimeException('No se pudo guardar la imagen de la variante.');
        }

        $oldImagePath = null;

        try {
            if ($this->editingId !== null) {
                $variant = SneakerVariant::findOrFail($this->editingId);
                $oldImagePath = $variant->image_path;

                if ($imagePath) {
                    $attributes['image_path'] = $imagePath;
                }

                $variant->update($attributes);
                $message = 'Variante actualizada.';
            } else {
                DB::transaction(function () use ($attributes, $data, $imagePath): void {
                    if ($imagePath) {
                        $attributes['image_path'] = $imagePath;
                    }

                    $variant = SneakerVariant::create($attributes + ['stock' => $data['initialStock']]);

                    if ($data['initialStock'] > 0) {
                        $variant->movements()->create([
                            'user_id' => auth()->id(),
                            'type' => 'in',
                            'quantity_change' => $data['initialStock'],
                            'note' => 'Stock inicial',
                        ]);
                    }
                });
                $message = 'Variante agregada al inventario.';
            }
        } catch (Throwable $exception) {
            if ($imagePath) {
                Storage::disk('public')->delete($imagePath);
            }

            throw $exception;
        }

        if ($imagePath && $oldImagePath) {
            Storage::disk('public')->delete($oldImagePath);
        }

        $this->showForm = false;
        $this->resetForm();
        session()->flash('swal', [
            'title' => 'SneakerStock',
            'text' => $message,
            'icon' => 'success',
            'confirmButtonText' => 'Continuar',
        ]);
    }

    public function openAdjustment(int $id): void
    {
        SneakerVariant::findOrFail($id);
        $this->resetValidation();
        $this->adjustingId = $id;
        $this->adjustmentType = 'in';
        $this->adjustmentQuantity = 1;
        $this->adjustmentNote = '';
    }

    public function adjustStock(): void
    {
        $data = $this->validate([
            'adjustingId' => ['required', 'integer', 'exists:sneaker_variants,id'],
            'adjustmentType' => ['required', 'in:in,out'],
            'adjustmentQuantity' => ['required', 'integer', 'min:1'],
            'adjustmentNote' => ['nullable', 'string', 'max:255'],
        ]);

        DB::transaction(function () use ($data): void {
            $variant = SneakerVariant::query()
                ->lockForUpdate()
                ->findOrFail($data['adjustingId']);
            $delta = $data['adjustmentType'] === 'in'
                ? $data['adjustmentQuantity']
                : -$data['adjustmentQuantity'];

            if ($variant->stock + $delta < 0) {
                throw ValidationException::withMessages([
                    'adjustmentQuantity' => 'La salida no puede superar los pares disponibles.',
                ]);
            }

            $variant->update(['stock' => $variant->stock + $delta]);
            $variant->movements()->create([
                'user_id' => auth()->id(),
                'type' => $data['adjustmentType'],
                'quantity_change' => $delta,
                'note' => filled($data['adjustmentNote']) ? trim($data['adjustmentNote']) : null,
            ]);
        });

        $this->adjustingId = null;
        $this->resetValidation();
        session()->flash('swal', [
            'title' => 'Stock actualizado',
            'text' => 'El movimiento quedó registrado.',
            'icon' => 'success',
            'confirmButtonText' => 'Continuar',
        ]);
    }

    public function deleteVariant(int $id): void
    {
        SneakerVariant::findOrFail($id)->delete();
    }

    private function resetForm(): void
    {
        $this->resetValidation();
        $this->editingId = null;
        $this->brand = '';
        $this->modelName = '';
        $this->sku = '';
        $this->color = '';
        $this->size = '';
        $this->categoryId = '';
        $this->salePrice = '';
        $this->initialStock = 0;
        $this->image = null;
        $this->existingImageUrl = null;
    }

    public function render()
    {
        $variants = SneakerVariant::query()
            ->with('category')
            ->when($this->search !== '', function ($query): void {
                $term = '%'.trim($this->search).'%';
                $query->where(function ($query) use ($term): void {
                    $query->where('brand', 'like', $term)
                        ->orWhere('model', 'like', $term)
                        ->orWhere('sku', 'like', $term)
                        ->orWhere('color', 'like', $term)
                        ->orWhere('size', 'like', $term);
                });
            })
            ->when($this->categoryFilter !== '', fn ($query) => $query->where('category_id', $this->categoryFilter))
            ->when($this->stockFilter === 'low', fn ($query) => $query->lowStock())
            ->when($this->stockFilter === 'out', fn ($query) => $query->where('stock', 0))
            ->when($this->stockFilter === 'available', fn ($query) => $query->where('stock', '>', 0))
            ->orderBy('brand')
            ->orderBy('model')
            ->orderBy('size')
            ->paginate(12);

        $summary = SneakerVariant::query()
            ->selectRaw('COUNT(*) as variants, COALESCE(SUM(stock), 0) as units, COALESCE(SUM(stock * sale_price), 0) as retail_value')
            ->first();

        return view('livewire.admin.inventory-index', [
            'variants' => $variants,
            'categories' => Category::query()->orderBy('name')->get(),
            'summary' => $summary,
            'lowStockCount' => SneakerVariant::lowStock()->count(),
            'recentMovements' => StockMovement::query()
                ->with('sneakerVariant')
                ->latest()
                ->limit(5)
                ->get(),
        ])->layout('layouts.app', ['title' => 'Inventario']);
    }
}
