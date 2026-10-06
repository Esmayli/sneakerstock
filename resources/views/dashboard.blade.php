<x-layouts::app :title="__('Resumen')">
    <div class="sneaker-page sneaker-dashboard">
        <div class="sneaker-content">
            <header class="sneaker-heading sneaker-dashboard__heading">
                <div>
                    <p class="sneaker-eyebrow">SNEAKERSTOCK <span>/</span> RESUMEN DE HOY</p>
                    <h1>El pulso de tu tienda.</h1>
                    <p class="sneaker-subtitle">Hola, {{ auth()->user()->name }}. Aquí está tu inventario en movimiento.</p>
                </div>
                @if (auth()->user()->hasRole('admin'))
                    <a href="{{ route('admin.inventory.index') }}" class="sneaker-button sneaker-button--primary">Abrir inventario <span aria-hidden="true">→</span></a>
                @endif
            </header>

            <section class="sneaker-stats" aria-label="Indicadores de inventario">
                <article class="sneaker-stat">
                    <span class="sneaker-stat__label">Variantes registradas</span>
                    <strong>{{ number_format($summary->variants) }}</strong>
                    <span class="sneaker-stat__hint">Modelos por color y talla</span>
                </article>
                <article class="sneaker-stat">
                    <span class="sneaker-stat__label">Pares en tienda</span>
                    <strong>{{ number_format($summary->units) }}</strong>
                    <span class="sneaker-stat__hint">Existencia total disponible</span>
                </article>
                <article class="sneaker-stat">
                    <span class="sneaker-stat__label">Valor potencial</span>
                    <strong class="sneaker-stat__currency">Bs {{ number_format((float) $summary->retail_value, 2, ',', '.') }}</strong>
                    <span class="sneaker-stat__hint">A precio de venta</span>
                </article>
            </section>

            <div class="sneaker-dashboard__columns">
                <section class="sneaker-panel sneaker-panel--dashboard" aria-labelledby="replenish-title">
                    <div class="sneaker-section-heading">
                        <div>
                            <p class="sneaker-eyebrow">ATENCIÓN</p>
                            <h2 id="replenish-title">Necesitan reposición</h2>
                        </div>
                        <span class="sneaker-count">{{ $lowStock->count() }}</span>
                    </div>
                    <div class="sneaker-low-stock-list">
                        @forelse ($lowStock as $variant)
                            <div class="sneaker-low-stock-row" wire:key="low-stock-{{ $variant->id }}">
                                <span class="sneaker-product__mark sneaker-product__mark--small" aria-hidden="true">{{ strtoupper(substr($variant->brand, 0, 1)) }}</span>
                                <span class="sneaker-low-stock-row__name">
                                    <strong>{{ $variant->brand }} {{ $variant->model }}</strong>
                                    <small>{{ $variant->color }} · Talla {{ $variant->size }}</small>
                                </span>
                                <span class="sneaker-stock sneaker-stock--low">{{ $variant->stock }} <small>/ {{ $variant->min_stock }}</small></span>
                            </div>
                        @empty
                            <div class="sneaker-dashboard-empty">
                                <span class="sneaker-dashboard-empty__check" aria-hidden="true">✓</span>
                                <strong>Todo en orden</strong>
                                <span>No hay variantes en nivel mínimo.</span>
                            </div>
                        @endforelse
                    </div>
                </section>

                <section class="sneaker-panel sneaker-panel--dashboard" aria-labelledby="activity-title">
                    <div class="sneaker-section-heading">
                        <div>
                            <p class="sneaker-eyebrow">TRAZABILIDAD</p>
                            <h2 id="activity-title">Actividad reciente</h2>
                        </div>
                        <span class="sneaker-section-heading__meta">Últimos 5</span>
                    </div>
                    <div class="sneaker-movement-list">
                        @forelse ($recentMovements as $movement)
                            <article class="sneaker-movement" wire:key="dashboard-movement-{{ $movement->id }}">
                                <span class="sneaker-movement__direction {{ $movement->quantity_change > 0 ? 'is-in' : 'is-out' }}" aria-hidden="true">{{ $movement->quantity_change > 0 ? '+' : '−' }}</span>
                                <div class="sneaker-movement__details">
                                    <strong>{{ $movement->sneakerVariant?->brand }} {{ $movement->sneakerVariant?->model }}</strong>
                                    <span>{{ $movement->note ?: ($movement->type === 'in' ? 'Entrada de stock' : 'Salida de stock') }}</span>
                                </div>
                                <strong class="sneaker-movement__quantity {{ $movement->quantity_change > 0 ? 'is-in' : 'is-out' }}">{{ $movement->quantity_change > 0 ? '+' : '' }}{{ $movement->quantity_change }}</strong>
                            </article>
                        @empty
                            <p class="sneaker-movement-empty">Cuando registres una entrada o salida, aparecerá aquí.</p>
                        @endforelse
                    </div>
                </section>
            </div>
        </div>
    </div>
</x-layouts::app>
