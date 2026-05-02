@extends('layouts.app')

@section('content')
<div class="position-absolute top-0 end-0 p-4 d-flex align-items-center gap-3">
    <div class="btn-group rounded-3 p-1" style="background: var(--input-bg);">
        @foreach(['uz' => 'UZ', 'ru' => 'RU', 'en' => 'EN'] as $locale => $label)
            <a href="{{ route('locale', $locale) }}" class="btn btn-sm border-0 {{ app()->getLocale() === $locale ? 'shadow-sm fw-bold text-body' : 'text-muted' }}" style="font-size: 0.75rem; background: {{ app()->getLocale() === $locale ? 'var(--card-bg)' : 'transparent' }};">
                {{ $label }}
            </a>
        @endforeach
    </div>
    <button class="btn btn-sm text-body shadow-sm rounded-circle d-flex align-items-center justify-content-center" id="themeToggleLogin" title="Toggle Theme" style="width: 36px; height: 36px; background: var(--card-bg);">
        <i class="bi bi-moon-stars-fill d-none dark-icon"></i>
        <i class="bi bi-sun-fill light-icon"></i>
    </button>
</div>

<div class="card border-0 shadow-lg overflow-hidden" style="width: 100%; max-width: 420px; border-radius: 1.5rem; background: var(--card-bg);">
    <div class="p-5 text-center border-bottom" style="border-color: var(--border-color) !important;">
        <div class="d-inline-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary rounded-4 mb-4" style="width: 64px; height: 64px; font-size: 1.75rem;">
            <i class="bi bi-rocket-takeoff-fill"></i>
        </div>
        <h3 class="fw-bold mb-1 text-body">{{ __('app.welcome') }}</h3>
        <p class="text-muted small">{{ __('app.login_hint') }}</p>

        <form method="POST" action="{{ route('login.store') }}" class="text-start mt-4">
            @csrf
            
            <div class="mb-3">
                <label class="form-label small fw-bold text-muted text-uppercase">{{ __('app.email') }}</label>
                <input type="email" class="form-control py-2 px-3" name="email" value="{{ old('email') }}" placeholder="admin@example.com" required autofocus>
            </div>
            
            <div class="mb-4">
                <label class="form-label small fw-bold text-muted text-uppercase">{{ __('app.password') }}</label>
                <input type="password" class="form-control py-2 px-3" name="password" placeholder="••••••••" required>
            </div>

            <div class="mb-4 d-flex justify-content-between align-items-center">
                <div class="form-check">
                    <input type="checkbox" class="form-check-input border-secondary" id="remember" name="remember">
                    <label class="form-check-label small text-body" for="remember">{{ __('app.remember') }}</label>
                </div>
            </div>

            <button type="submit" class="btn btn-primary w-100 py-2 fw-bold shadow-sm border-0 mt-2" style="background: linear-gradient(135deg, #4f46e5, #6366f1);">
                {{ __('app.login') }} <i class="bi bi-arrow-right ms-2"></i>
            </button>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    const themeToggleLogin = document.getElementById('themeToggleLogin');
    if (themeToggleLogin) {
        themeToggleLogin.addEventListener('click', () => {
            const currentTheme = document.documentElement.getAttribute('data-bs-theme');
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            document.documentElement.setAttribute('data-bs-theme', newTheme);
            localStorage.setItem('theme', newTheme);
            
            // Layoutdagi ikonalarni ham yangilaymiz (agar mavjud bo'lsa)
            if(typeof updateThemeUI === 'function') {
                updateThemeUI(newTheme);
            } else {
                // Faqat shu sahifadagi ikonalarni yangilash
                const darkIconL = document.querySelector('#themeToggleLogin .dark-icon');
                const lightIconL = document.querySelector('#themeToggleLogin .light-icon');
                if (newTheme === 'dark') {
                    darkIconL.classList.remove('d-none');
                    lightIconL.classList.add('d-none');
                } else {
                    lightIconL.classList.remove('d-none');
                    darkIconL.classList.add('d-none');
                }
            }
        });
        
        // Initial setup for login page toggle icons
        const initTheme = getPreferredTheme();
        const darkIconL = document.querySelector('#themeToggleLogin .dark-icon');
        const lightIconL = document.querySelector('#themeToggleLogin .light-icon');
        if (initTheme === 'dark') {
            darkIconL.classList.remove('d-none');
            lightIconL.classList.add('d-none');
        } else {
            lightIconL.classList.remove('d-none');
            darkIconL.classList.add('d-none');
        }
    }
</script>
@endsection
