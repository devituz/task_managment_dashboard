@extends('layouts.app')

@section('title', __('app.profile'))

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm overflow-hidden" style="border-radius: 1.5rem;">
            <div class="p-5 text-center text-white position-relative" style="background: linear-gradient(135deg, var(--primary-color), #0f172a);">
                <div class="position-relative d-inline-block mb-3">
                    <img id="avatarPreview" src="{{ $user->avatar_url }}" class="rounded-circle shadow-lg" style="width: 100px; height: 100px; object-fit: cover; border: 4px solid rgba(255,255,255,0.2);">
                    <label for="avatar" class="position-absolute bottom-0 end-0 bg-white text-primary rounded-circle d-flex align-items-center justify-content-center cursor-pointer shadow" style="width: 32px; height: 32px; cursor: pointer; border: 2px solid var(--primary-color);">
                        <i class="bi bi-camera-fill"></i>
                    </label>
                </div>
                <h4 class="mb-1 fw-bold">{{ $user->name }}</h4>
                <p class="text-white-50 small mb-0">{{ $user->role === 'superadmin' ? __('app.role_superadmin') : __('app.role_employer') }}</p>
            </div>
            <div class="card-body p-4 p-md-5">
                <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <input type="file" name="avatar" id="avatar" class="d-none" accept="image/*" onchange="previewAvatar(event)">

                    <div class="mb-4">
                        <label class="form-label fw-semibold text-muted small text-uppercase">{{ __('app.full_name') }}</label>
                        <input type="text" name="name" class="form-control bg-light border-0 py-2 @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required>
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold text-muted small text-uppercase">{{ __('app.email') }}</label>
                        <input type="email" name="email" class="form-control bg-light border-0 py-2 @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required>
                        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold text-muted small text-uppercase">{{ __('app.telegram_id') }}</label>
                        <input type="text" name="telegram_id" class="form-control bg-light border-0 py-2 @error('telegram_id') is-invalid @enderror" value="{{ old('telegram_id', $user->telegram_id) }}">
                        <div class="form-text">{{ __('app.telegram_hint') }}</div>
                        @error('telegram_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <hr class="my-5 border-light">

                    <h5 class="fw-bold mb-4">{{ __('app.change_password') }}</h5>
                    
                    <div class="mb-4">
                        <label class="form-label fw-semibold text-muted small text-uppercase">{{ __('app.new_password') }}</label>
                        <input type="password" name="password" class="form-control bg-light border-0 py-2 @error('password') is-invalid @enderror">
                        <div class="form-text">{{ __('app.leave_blank') }}</div>
                        @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-5">
                        <label class="form-label fw-semibold text-muted small text-uppercase">{{ __('app.confirm_password') }}</label>
                        <input type="password" name="password_confirmation" class="form-control bg-light border-0 py-2">
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-lg fw-bold shadow-sm" style="border-radius: 12px;">{{ __('app.update_profile') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function previewAvatar(event) {
        if (event.target.files.length > 0) {
            var src = URL.createObjectURL(event.target.files[0]);
            var preview = document.getElementById('avatarPreview');
            preview.src = src;
        }
    }
</script>
@endsection