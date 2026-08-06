# Spec 12: Blade UI Components & Accom Branding

## Metadata
- **User Story**: [US-001](../features/20260511-user-management-and-application-bootstrap.md)
- **Type**: Frontend (Blade + Tailwind CSS + Alpine.js)
- **Branch**: feature/20260511_user_management_and_application_bootstrap
- **Estimated effort**: M
- **Depends on**: Spec 0 (Tailwind brand tokens), Spec 1 (Breeze scaffold), Spec 9 (dashboard controller)

## Overview
Implement all visual Blade components and branding:
1. `<x-navbar>` — top navigation bar with inline Accom SVG logo and logout link.
2. `<x-clock-widget>` — Factorial-inspired card with status dot and action button.
3. Update `resources/views/layouts/app.blade.php` to use `<x-navbar>`.
4. Style the login page per AC-24.
5. Visual verification: `npm run dev` + manual browser check of `/login` and `/dashboard`.

## Architecture Context
- `resources/views/components/navbar.blade.php`
- `resources/views/components/clock-widget.blade.php`
- `resources/views/layouts/app.blade.php` (Breeze default, customise)
- `resources/views/auth/login.blade.php` (Breeze default, customise)
- All styling: Tailwind utility classes only. No `.css` files.
- UI text in **Spanish**; code identifiers in **English**.

## Implementation Steps

### Step 0: Verify on feature branch.

### Step 1: Create `<x-navbar>` component
- **File**: `resources/views/components/navbar.blade.php`
```blade
@props(['showUserInfo' => true])

<nav class="bg-white border-b border-gray-200 h-16 flex items-center px-6 justify-between">
    {{-- Accom logo (inline SVG, brand teal #2BBFB3) --}}
    <a href="{{ route('dashboard') }}" class="flex items-center">
        <svg width="120" height="32" viewBox="0 0 300 80" fill="none" xmlns="http://www.w3.org/2000/svg">
            <!-- Simplified wordmark: "accom" in teal with dot-people M in pink -->
            <text x="0" y="60" font-family="Arial,sans-serif" font-weight="700"
                  font-size="72" fill="#2BBFB3">accom</text>
            <!-- Pink dot above the i (approximated) -->
            <circle cx="198" cy="8" r="8" fill="#E8195A"/>
        </svg>
    </a>

    @if($showUserInfo && auth()->check())
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
```
Note: The SVG is a simplified representation. Replace with the exact Accom SVG file if provided by the design team; otherwise use this approximation until official assets are available.

### Step 2: Create `<x-clock-widget>` component
- **File**: `resources/views/components/clock-widget.blade.php`
```blade
@props(['hasOpenRecord' => false])

<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 max-w-xs">
    {{-- Card header --}}
    <div class="flex items-center justify-between mb-4">
        <span class="text-sm font-medium text-gray-500">Fichaje</span>
        <span class="text-gray-400 text-sm">›</span>
    </div>

    {{-- Status row --}}
    <div class="flex items-center gap-2 mb-4">
        @if($hasOpenRecord)
            <span class="w-2.5 h-2.5 rounded-full bg-accom-teal animate-pulse"></span>
            <span class="text-accom-teal font-semibold text-sm">Fichado</span>
        @else
            <span class="w-2.5 h-2.5 rounded-full bg-gray-300"></span>
            <span class="text-gray-400 text-sm">Sin fichar</span>
        @endif
    </div>

    {{-- Action button (placeholder — POST wired in US-002) --}}
    @if($hasOpenRecord)
        <button type="button"
            class="inline-flex items-center gap-2 bg-accom-pink text-white rounded-lg px-5 py-2.5 font-medium text-sm hover:bg-pink-700 transition">
            <span>■</span> Salida
        </button>
    @else
        <button type="button"
            class="bg-accom-teal text-white rounded-lg px-5 py-2.5 font-medium text-sm hover:bg-teal-600 transition">
            Entrada
        </button>
    @endif

    {{-- US-002 placeholder note (visible only in local dev via @env) --}}
    @env('local')
        <p class="mt-3 text-xs text-gray-400">⚠ Clock-in/out action wired in US-002.</p>
    @endenv
</div>
```

### Step 3: Update `resources/views/layouts/app.blade.php`
- Replace the default Breeze navbar section with `<x-navbar />`.
- Set page background: `<body class="bg-gray-50 ...">`.

### Step 4: Style the login page (AC-24)
- **File**: `resources/views/auth/login.blade.php`
- Replace the default Breeze login layout with:
  - Accom logo centred via `<x-navbar :showUserInfo="false" />` (or directly inline the SVG)
  - Card: `max-w-md mx-auto mt-24 bg-white rounded-xl shadow-md p-8`
  - Submit button: `bg-accom-teal text-white w-full rounded-lg py-2.5 font-medium`
  - Error messages: `text-accom-pink text-sm`

### Step 5: Visual verification
```bash
npm run dev
php artisan serve
```
- Open `http://localhost:8000/login` — verify Accom logo, teal button, pink errors.
- Login as an employee — verify navbar (logo + name + "Cerrar sesión"), clock widget card, correct button state.
- Login as admin — verify redirect to `/admin` (Filament default theme, no Accom branding applied).

### Step LAST: Update Documentation
- Note in `README.md`: "Logo SVG is approximated; replace with official asset when available."

## Unit Test Specifications (TDD)
No automated tests for pure Blade/CSS components. Verification is the visual check in Step 5.

## Acceptance Criteria
- [ ] `<x-navbar>` renders Accom logo (SVG, teal) and user info on all authenticated pages.
- [ ] Login page uses `<x-navbar :showUserInfo="false">` or centred logo; teal submit button; pink error text.
- [ ] `<x-clock-widget hasOpenRecord=false>` shows "Sin fichar" dot and "Entrada" teal button.
- [ ] `<x-clock-widget hasOpenRecord=true>` shows "Fichado" pulsing dot and "Salida" pink button.
- [ ] `npm run dev` compiles without errors.
- [ ] Manual visual check of `/login` and `/dashboard` passes.
- [ ] No custom `.css` files created.
- [ ] Branch follows naming convention.
- [ ] PR created in Bitbucket.
