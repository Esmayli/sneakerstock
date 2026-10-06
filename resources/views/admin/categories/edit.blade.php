<x-layouts::app :title="__('Editar categoría')">
    <div class="sneaker-page sneaker-category-page">
        <div class="sneaker-content">
            <header class="sneaker-heading">
                <div>
                    <nav class="sneaker-breadcrumbs" aria-label="Ruta de navegación">
                        <a href="{{ route('dashboard') }}">Resumen</a>
                        <span aria-hidden="true">/</span>
                        <a href="{{ route('admin.categories.index') }}">Categorías</a>
                        <span aria-hidden="true">/</span>
                        <span aria-current="page">Editar</span>
                    </nav>
                    <p class="sneaker-eyebrow">CATÁLOGO <span>/</span> ORGANIZACIÓN</p>
                    <h1>Editar categoría</h1>
                    <p class="sneaker-subtitle">Actualiza el nombre o la imagen de <span class="notranslate" translate="no">{{ $category->name }}</span>.</p>
                </div>
                <a href="{{ route('admin.categories.index') }}" class="sneaker-button sneaker-button--quiet">Cancelar</a>
            </header>

            <section class="sneaker-panel sneaker-category-form-panel">
                <form action="{{ route('admin.categories.update', $category) }}" method="POST" enctype="multipart/form-data" class="sneaker-category-form">
                    @csrf
                    @method('PUT')
                    <div class="sneaker-category-upload">
                        <img
                            id="imgPreview"
                            src="{{ $category->image_path ? route('admin.categories.image', $category) : '' }}"
                            alt="Vista previa de {{ $category->name }}"
                            @if (! $category->image_path) hidden @endif
                            onerror="this.hidden = true; this.nextElementSibling.hidden = false"
                        >
                        <div class="sneaker-category-upload__fallback" @if ($category->image_path) hidden @endif>
                            <span aria-hidden="true">{{ strtoupper(substr($category->name, 0, 1)) }}</span>
                            <small>Vista previa</small>
                        </div>
                        <label class="sneaker-category-upload__button">
                            Cambiar imagen
                            <input onchange="previewImage(event, '#imgPreview')" name="image" accept="image/jpeg,image/png,image/gif,image/bmp,image/webp,image/avif" type="file">
                        </label>
                    </div>

                    <div class="sneaker-category-form__fields">
                        <label class="sneaker-field">
                            <span>Nombre de la categoría</span>
                            <input name="name" value="{{ old('name', $category->name) }}" required maxlength="255" class="notranslate" translate="no">
                            @error('name') <small>{{ $message }}</small> @enderror
                        </label>
                        @error('image') <small class="sneaker-form-error">{{ $message }}</small> @enderror
                    </div>

                    <div class="sneaker-modal__actions">
                        <a href="{{ route('admin.categories.index') }}" class="sneaker-button sneaker-button--quiet">Cancelar</a>
                        <button type="submit" class="sneaker-button sneaker-button--primary">Guardar cambios</button>
                    </div>
                </form>
            </section>
        </div>
    </div>
</x-layouts::app>