<div class="sneaker-page sneaker-page--users">
    <div class="sneaker-content">
        <header class="sneaker-heading sneaker-users__heading">
            <div>
                <p class="sneaker-eyebrow">SNEAKERSTOCK <span>/</span> EQUIPO DE TIENDA</p>
                <h1>Usuarios</h1>
                <p class="sneaker-subtitle">Personas con acceso a la operación de tu tienda.</p>
            </div>
            <div class="sneaker-users__count" aria-label="{{ number_format($users->total()) }} usuarios registrados">
                <strong>{{ number_format($users->total()) }}</strong>
                <span>registrados</span>
            </div>
        </header>

        <section class="sneaker-panel sneaker-users" aria-label="Listado de usuarios">
            <div class="sneaker-toolbar">
                <label class="sneaker-search">
                    <span class="sneaker-sr-only">Buscar usuarios por nombre o correo</span>
                    <span class="sneaker-search__icon" aria-hidden="true">⌕</span>
                    <input
                        type="search"
                        wire:model.live.debounce.300ms="search"
                        placeholder="Buscar por nombre o correo"
                        aria-label="Buscar usuarios"
                    >
                </label>
                <span class="sneaker-users__result">{{ number_format($users->total()) }} {{ $users->total() === 1 ? 'usuario' : 'usuarios' }}</span>
            </div>

            <div class="sneaker-table-wrap">
                <table class="sneaker-table sneaker-users-table">
                    <thead>
                        <tr>
                            <th scope="col">ID</th>
                            <th scope="col">Nombre</th>
                            <th scope="col">Correo</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($users as $user)
                            <tr wire:key="user-{{ $user->id }}">
                                <td><span class="sneaker-user__id">{{ $user->id }}</span></td>
                                <td>
                                    <div class="sneaker-user">
                                        <span class="sneaker-user__mark {{ $loop->even ? 'sneaker-user__mark--orange' : '' }}" aria-hidden="true">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                                        <strong>{{ $user->name }}</strong>
                                    </div>
                                </td>
                                <td class="sneaker-user__email">{{ $user->email }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3">
                                    <div class="sneaker-empty">
                                        <span class="sneaker-empty__mark" aria-hidden="true">S</span>
                                        <strong>No encontramos usuarios</strong>
                                        <span>Prueba con otro nombre o correo.</span>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($users->hasPages())
                <div class="sneaker-pagination">{{ $users->links() }}</div>
            @endif
        </section>
    </div>
</div>
