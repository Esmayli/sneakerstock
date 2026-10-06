    <x-layouts::app :title="__('Categorías')">
        <div class="sneaker-page sneaker-category-page">
            <div class="sneaker-content">
                <header class="sneaker-heading">
                    <div>
                        <nav class="sneaker-breadcrumbs" aria-label="Ruta de navegación">
                            <a href="{{ route('dashboard') }}">Resumen</a>
                            <span aria-hidden="true">/</span>
                            <span aria-current="page">Categorías</span>
                        </nav>
                        <p class="sneaker-eyebrow">SNEAKERSTOCK <span>/</span> ORGANIZACIÓN</p>
                        <h1>Categorías</h1>
                        <p class="sneaker-subtitle">Ordena tu catálogo y encuentra cada par más rápido.</p>
                    </div>
                    <a href="{{ route('admin.categories.create') }}" class="sneaker-button sneaker-button--primary">
                        <span aria-hidden="true">+</span> Nueva categoría
                    </a>
                </header>

                <section class="sneaker-panel sneaker-category-panel" aria-label="Categorías de la tienda">
                    <div class="sneaker-category-toolbar">
                        <div>
                            <p class="sneaker-eyebrow">CATÁLOGO</p>
                            <h2>Familias de producto</h2>
                        </div>
                        <span class="sneaker-category-count">{{ $categories->count() }} <small>{{ $categories->count() === 1 ? 'categoría' : 'categorías' }}</small></span>
                    </div>

                    <div class="sneaker-table-wrap">
                        <table class="sneaker-table sneaker-category-table">
                            <thead>
                                <tr>
                                    <th scope="col">Categoría</th>
                                    <th scope="col">Imagen</th>
                                    <th scope="col"><span class="sneaker-sr-only">Acciones</span></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($categories as $category)
                                    <tr wire:key="category-{{ $category->id }}">
                                        <th scope="row">
                                            <div class="sneaker-category-name">
                                                <span class="sneaker-category-index">{{ str_pad((string) $category->id, 2, '0', STR_PAD_LEFT) }}</span>
                                                <span class="notranslate" translate="no">{{ $category->name }}</span>
                                            </div>
                                        </th>
                                        <td>
                                            <div class="sneaker-category-thumb" aria-label="Imagen de {{ $category->name }}">
                                                @if ($category->image_path)
                                                    <img
                                                        src="{{ route('admin.categories.image', $category) }}"
                                                        alt="{{ $category->name }}"
                                                        onerror="this.hidden = true; this.nextElementSibling.hidden = false"
                                                    >
                                                    <span class="sneaker-category-thumb__fallback" hidden>{{ strtoupper(substr($category->name, 0, 1)) }}</span>
                                                @else
                                                    <span class="sneaker-category-thumb__fallback">{{ strtoupper(substr($category->name, 0, 1)) }}</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <div class="sneaker-category-actions">
                                                <a href="{{ route('admin.categories.edit', $category) }}" class="sneaker-button sneaker-button--quiet">Editar</a>
                                                <form class="delete-form" action="{{ route('admin.categories.destroy', $category) }}" method="post">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="sneaker-button sneaker-button--delete">Eliminar</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3">
                                            <div class="sneaker-empty">
                                                <span class="sneaker-empty__mark" aria-hidden="true">S</span>
                                                <strong>Aún no hay categorías</strong>
                                                <span>Crea la primera para organizar tus tenis.</span>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </section>
            </div>
        </div>

        @push('js')
            <script>
                document.querySelectorAll('.delete-form').forEach((form) => {
                    form.addEventListener('submit', (event) => {
                        event.preventDefault();
                        Swal.fire({
                            title: '¿Eliminar categoría?',
                            text: 'Esta acción no se puede deshacer.',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#a9443d',
                            cancelButtonColor: '#65766d',
                            confirmButtonText: 'Sí, eliminar',
                            cancelButtonText: 'Cancelar',
                        }).then((result) => {
                            if (result.isConfirmed) form.submit();
                        });
                    });
                });
            </script>
        @endpush
    </x-layouts::app>