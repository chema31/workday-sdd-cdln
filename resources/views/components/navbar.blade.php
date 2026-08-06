@props(['showUserInfo' => true])

<nav class="bg-white border-b border-gray-200 h-16 flex items-center px-6 justify-between">
    <a href="{{ route('dashboard') }}" class="flex items-center" aria-label="Inicio">
        <x-brand-logo />
    </a>

    @if ($showUserInfo && auth()->check())
        <div class="flex items-center gap-4">
            <span class="text-sm text-gray-700">{{ auth()->user()->name }}</span>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    class="text-sm text-gray-500 hover:text-accom-pink border border-gray-300 rounded-lg px-3 py-1.5 hover:border-accom-pink transition">
                    Cerrar sesión
                </button>
            </form>
        </div>
    @endif
</nav>
