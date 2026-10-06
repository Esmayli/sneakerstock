<x-layouts::app :title="__('Nueva categoría')">
    <div class="sneaker-page sneaker-category-page">
        <div class="sneaker-content">
            <header class="sneaker-heading">
                <div>
                    <nav class="sneaker-breadcrumbs" aria-label="Ruta de navegación">
                        <a href="{{ route('dashboard') }}">Resumen</a>
                        <span aria-hidden="true">/</span>
                        <a href="{{ route('admin.categories.index') }}">Categorías</a>
                        <span aria-hidden="true">/</span>
                        <span aria-current="page">Nueva</span>
                    </nav>
                    <p class="sneaker-eyebrow">CATÁLOGO <span>/</span> ORGANIZACIÓN</p>
                    <h1>Nueva categoría</h1>
                    <p class="sneaker-subtitle">Dale una identidad clara a una familia de tenis.</p>
                </div>
                <a href="{{ route('admin.categories.index') }}" class="sneaker-button sneaker-button--quiet">Cancelar</a>
            </header>

            <section class="sneaker-panel sneaker-category-form-panel">
                <form action="{{ route('admin.categories.store') }}" method="POST" enctype="multipart/form-data" class="sneaker-category-form">
                    @csrf
                    <div class="sneaker-category-upload">
                        <img id="imgPreview" alt="Vista previa de la categoría" hidden>
                        <div class="sneaker-category-upload__fallback">
                            <span aria-hidden="true">S</span>
                            <small>Vista previa</small>
                        </div>
                        <label class="sneaker-category-upload__button">
                            Elegir imagen
                            <input onchange="previewImage(event, '#imgPreview')" name="image" accept="image/jpeg,image/png,image/gif,image/bmp,image/webp,image/avif" type="file">
                        </label>
                    </div>

                    <div class="sneaker-category-form__fields">
                        <label class="sneaker-field">
                            <span>Nombre de la categoría</span>
                            <input name="name" value="{{ old('name') }}" placeholder="Ej. Running, Casual, Skate" required maxlength="255" class="notranslate" translate="no">
                            @error('name') <small>{{ $message }}</small> @enderror
                        </label>
                        @error('image') <small class="sneaker-form-error">{{ $message }}</small> @enderror
                    </div>

                    <div class="sneaker-modal__actions">
                        <a href="{{ route('admin.categories.index') }}" class="sneaker-button sneaker-button--quiet">Cancelar</a>
                        <button type="submit" class="sneaker-button sneaker-button--primary">Crear categoría</button>
                    </div>
                </form>
            </section>
        </div>
    </div>
</x-layouts::app>