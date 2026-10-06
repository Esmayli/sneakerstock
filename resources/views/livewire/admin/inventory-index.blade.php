<div class="sneaker-page sneaker-page--inventory">
    <div class="sneaker-content">
        <header class="sneaker-heading sneaker-heading--inventory">
            <div>
                <p class="sneaker-eyebrow">SNEAKERSTOCK <span>/</span> CONTROL DE EXISTENCIAS</p>
                <h1>Inventario</h1>
                <p class="sneaker-subtitle">Cada talla cuenta. Mantén el pulso de tu tienda.</p>
            </div>
            <button type="button" class="sneaker-button sneaker-button--primary" wire:click="openCreate">
                <span aria-hidden="true">+</span> Agregar variante
            </button>
        </header>

        <section class="sneaker-stats" aria-label="Resumen del inventario">
            <article class="sneaker-stat">
                <span class="sneaker-stat__label">Variantes activas</span>
                <strong>{{ number_format($summary->variants) }}</strong>
                <span class="sneaker-stat__hint">Modelo, color y talla</span>
            </article>
            <article class="sneaker-stat">
                <span class="sneaker-stat__label">Pares disponibles</span>
                <strong>{{ number_format($summary->units) }}</strong>
                <span class="sneaker-stat__hint">Pares registrados en inventario</span>
            </article>
            <article class="sneaker-stat">
                <span class="sneaker-stat__label">Por reponer</span>
                <strong>{{ number_format($lowStockCount) }}</strong>
                <span class="sneaker-stat__hint">En mínimo o agotadas</span>
            </article>
            <article class="sneaker-stat">
                <span class="sneaker-stat__label">Valor a precio de venta</span>
                <strong class="sneaker-stat__currency">Bs {{ number_format((float) $summary->retail_value, 2, ',', '.') }}</strong>
                <span class="sneaker-stat__hint">Inventario actual</span>
            </article>
        </section>

        <section class="sneaker-panel" aria-label="Listado de variantes">
            <div class="sneaker-toolbar">
                <label class="sneaker-search">
                    <span class="sneaker-sr-only">Buscar por modelo, SKU, color o talla</span>
                    <span class="sneaker-search__icon" aria-hidden="true">⌕</span>
                    <input type="search" wire:model.live.debounce.300ms="search" placeholder="Buscar modelo, SKU, color o talla">
                </label>
                <label class="sneaker-filter">
                    <span class="sneaker-sr-only">Filtrar por categoría</span>
                    <select wire:model.live="categoryFilter">
                        <option value="">Todas las categorías</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" class="notranslate" translate="no">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </label>
                <label class="sneaker-filter">
                    <span class="sneaker-sr-only">Filtrar por existencias</span>
                    <select wire:model.live="stockFilter">
                        <option value="all">Todo el stock</option>
                        <option value="available">Con existencias</option>
                        <option value="low">Stock bajo</option>
                        <option value="out">Agotados</option>
                    </select>
                </label>
            </div>

            <div class="sneaker-table-wrap">
                <table class="sneaker-table">
                    <thead>
                        <tr>
                            <th scope="col">Modelo / SKU</th>
                            <th scope="col">Color</th>
                            <th scope="col">Talla</th>
                            <th scope="col">Precio</th>
                            <th scope="col">Stock</th>
                            <th scope="col"><span class="sneaker-sr-only">Acciones</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($variants as $variant)
                            <tr wire:key="variant-{{ $variant->id }}">
                                <td>
                                    <div class="sneaker-product">
                                        @if ($variant->image_path)
                                            <img class="sneaker-product__image" src="{{ route('admin.inventory.image', $variant) }}" alt="Tenis {{ $variant->brand }} {{ $variant->model }}">
                                        @else
                                            <span class="sneaker-product__mark" aria-hidden="true">{{ strtoupper(substr($variant->brand, 0, 1)) }}</span>
                                        @endif
                                        <span>
                                            <strong>{{ $variant->brand }} {{ $variant->model }}</strong>
                                            <small>{{ $variant->sku }}@if ($variant->category) <span>· <span class="notranslate" translate="no">{{ $variant->category->name }}</span></span>@endif</small>
                                        </span>
                                    </div>
                                </td>
                                <td>{{ $variant->color }}</td>
                                <td><span class="sneaker-size">{{ $variant->size }}</span></td>
                                <td>Bs {{ number_format((float) $variant->sale_price, 2, ',', '.') }}</td>
                                <td>
                                    <span class="sneaker-stock {{ $variant->stock <= $variant->min_stock ? 'sneaker-stock--low' : 'sneaker-stock--ok' }}">
                                        {{ $variant->stock }}
                                    </span>
                                    @if ($variant->stock <= $variant->min_stock)
                                        <small class="sneaker-stock__note">Reponer</small>
                                    @endif
                                </td>
                                <td>
                                    <div class="sneaker-actions">
                                        <button type="button" class="sneaker-link" wire:click="openAdjustment({{ $variant->id }})">Ajustar</button>
                                        <button type="button" class="sneaker-link" wire:click="openEdit({{ $variant->id }})">Editar</button>
                                        <button type="button" class="sneaker-link sneaker-link--danger" wire:click="deleteVariant({{ $variant->id }})" wire:confirm="¿Retirar esta variante del inventario?">Retirar</button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6">
                                    <div class="sneaker-empty">
                                        <span class="sneaker-empty__mark" aria-hidden="true">S</span>
                                        <strong>No encontramos variantes</strong>
                                        <span>Ajusta la búsqueda o agrega el primer par.</span>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($variants->hasPages())
                <div class="sneaker-pagination">{{ $variants->links() }}</div>
            @endif
        </section>

        <section class="sneaker-movements" aria-labelledby="movement-title">
            <div class="sneaker-section-heading">
                <div>
                    <p class="sneaker-eyebrow">TRAZABILIDAD</p>
                    <h2 id="movement-title">Movimientos recientes</h2>
                </div>
                <span>Últimos 5 registros</span>
            </div>
            <div class="sneaker-movement-list">
                @forelse ($recentMovements as $movement)
                    <article class="sneaker-movement" wire:key="movement-{{ $movement->id }}">
                        <span class="sneaker-movement__direction {{ $movement->quantity_change > 0 ? 'is-in' : 'is-out' }}" aria-hidden="true">
                            {{ $movement->quantity_change > 0 ? '+' : '−' }}
                        </span>
                        <div class="sneaker-movement__details">
                            <strong>{{ $movement->sneakerVariant?->brand }} {{ $movement->sneakerVariant?->model }}</strong>
                            <span>{{ $movement->note ?: ($movement->type === 'in' ? 'Entrada de stock' : 'Salida de stock') }}</span>
                        </div>
                        <time datetime="{{ $movement->created_at->toIso8601String() }}">{{ $movement->created_at->format('d/m H:i') }}</time>
                        <strong class="sneaker-movement__quantity {{ $movement->quantity_change > 0 ? 'is-in' : 'is-out' }}">
                            {{ $movement->quantity_change > 0 ? '+' : '' }}{{ $movement->quantity_change }}
                        </strong>
                    </article>
                @empty
                    <p class="sneaker-movement-empty">Los ajustes que registres aparecerán aquí.</p>
                @endforelse
            </div>
        </section>
    </div>

    @if ($showForm)
        <div class="sneaker-modal-backdrop" wire:click.self="$set('showForm', false)">
            <section class="sneaker-modal" role="dialog" aria-modal="true" aria-labelledby="variant-form-title">
                <div class="sneaker-modal__heading">
                    <div>
                        <p class="sneaker-eyebrow">CATÁLOGO</p>
                        <h2 id="variant-form-title">{{ $editingId ? 'Editar variante' : 'Nueva variante' }}</h2>
                    </div>
                    <button type="button" class="sneaker-modal__close" aria-label="Cerrar" wire:click="$set('showForm', false)">×</button>
                </div>

                <form wire:submit="saveVariant" class="sneaker-form">
                    <div class="sneaker-form-grid">
                        <label class="sneaker-field">
                            <span>Marca</span>
                            <input wire:model="brand" autocomplete="organization" placeholder="Nike, Adidas...">
                            @error('brand') <small>{{ $message }}</small> @enderror
                        </label>
                        <label class="sneaker-field">
                            <span>Modelo</span>
                            <input wire:model="modelName" placeholder="Air Max 90">
                            @error('modelName') <small>{{ $message }}</small> @enderror
                        </label>
                        <label class="sneaker-field">
                            <span>SKU único</span>
                            <input wire:model="sku" placeholder="NK-AM90-BLK-27">
                            @error('sku') <small>{{ $message }}</small> @enderror
                        </label>
                        <label class="sneaker-field">
                            <span>Categoría</span>
                            <select wire:model="categoryId">
                                <option value="">Sin categoría</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" class="notranslate" translate="no">{{ $category->name }}</option>
                                @endforeach
                            </select>
                            @error('categoryId') <small>{{ $message }}</small> @enderror
                        </label>
                        <label class="sneaker-field">
                            <span>Color</span>
                            <input wire:model="color" placeholder="Negro / blanco">
                            @error('color') <small>{{ $message }}</small> @enderror
                        </label>
                        <label class="sneaker-field">
                            <span>Talla</span>
                            <input wire:model="size" placeholder="27 o 27.5">
                            @error('size') <small>{{ $message }}</small> @enderror
                        </label>
                        <label class="sneaker-field">
                            <span>Precio de venta (Bs)</span>
                            <input type="number" min="0" max="500" step="0.01" wire:model="salePrice" placeholder="Hasta 500.00">
                            @error('salePrice') <small>{{ $message }}</small> @enderror
                        </label>
                        @unless ($editingId)
                            <label class="sneaker-field">
                                <span>Existencia inicial (pares)</span>
                                <input type="number" min="0" step="1" wire:model="initialStock">
                                @error('initialStock') <small>{{ $message }}</small> @enderror
                            </label>
                        @endunless
                    </div>
                    <div class="sneaker-image-upload">
                        <label class="sneaker-field">
                            <span>Imagen del tenis (opcional)</span>
                            <input type="file" wire:model="image" accept="image/jpeg,image/png,image/gif,image/bmp,image/webp,image/avif">
                            <small>JPG, PNG, GIF, BMP, WebP o AVIF; máximo 50 MB.</small>
                            @error('image') <small>{{ $message }}</small> @enderror
                        </label>
                        <div class="sneaker-image-upload__preview">
                            @if ($image)
                                <img src="{{ $image->temporaryUrl() }}" alt="Vista previa de la imagen del tenis">
                            @elseif ($existingImageUrl)
                                <img src="{{ $existingImageUrl }}" alt="Imagen actual del tenis">
                            @else
                                <span>{{ strtoupper(substr($brand ?: 'T', 0, 1)) }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="sneaker-modal__actions">
                        <button type="button" class="sneaker-button sneaker-button--quiet" wire:click="$set('showForm', false)">Cancelar</button>
                        <button type="submit" class="sneaker-button sneaker-button--primary" wire:loading.attr="disabled" wire:target="saveVariant">
                            <span wire:loading.remove wire:target="saveVariant">{{ $editingId ? 'Guardar cambios' : 'Crear variante' }}</span>
                            <span wire:loading wire:target="saveVariant">Guardando…</span>
                        </button>
                    </div>
                </form>
            </section>
        </div>
    @endif

    @if ($adjustingId)
        <div class="sneaker-modal-backdrop" wire:click.self="$set('adjustingId', null)">
            <section class="sneaker-modal sneaker-modal--compact" role="dialog" aria-modal="true" aria-labelledby="adjustment-title">
                <div class="sneaker-modal__heading">
                    <div>
                        <p class="sneaker-eyebrow">CONTROL DE STOCK</p>
                        <h2 id="adjustment-title">Registrar movimiento</h2>
                    </div>
                    <button type="button" class="sneaker-modal__close" aria-label="Cerrar" wire:click="$set('adjustingId', null)">×</button>
                </div>
                <form wire:submit="adjustStock" class="sneaker-form">
                    <div class="sneaker-segmented" role="group" aria-label="Tipo de movimiento">
                        <button type="button" class="{{ $adjustmentType === 'in' ? 'is-active' : '' }}" wire:click="$set('adjustmentType', 'in')">Entrada</button>
                        <button type="button" class="{{ $adjustmentType === 'out' ? 'is-active' : '' }}" wire:click="$set('adjustmentType', 'out')">Salida</button>
                    </div>
                    <label class="sneaker-field">
                        <span>Cantidad de pares</span>
                        <input type="number" min="1" step="1" wire:model="adjustmentQuantity">
                        @error('adjustmentQuantity') <small>{{ $message }}</small> @enderror
                    </label>
                    <label class="sneaker-field">
                        <span>Nota (opcional)</span>
                        <input wire:model="adjustmentNote" placeholder="Compra, devolución, merma...">
                        @error('adjustmentNote') <small>{{ $message }}</small> @enderror
                    </label>
                    <div class="sneaker-modal__actions">
                        <button type="button" class="sneaker-button sneaker-button--quiet" wire:click="$set('adjustingId', null)">Cancelar</button>
                        <button type="submit" class="sneaker-button sneaker-button--primary">Guardar movimiento</button>
                    </div>
                </form>
            </section>
        </div>
    @endif
</div>
