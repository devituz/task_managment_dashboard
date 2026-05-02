@extends('layouts.app')

@section('title', isset($employee->id) ? __('app.edit') : __('app.add_employee'))

@section('content')
<div class="mb-4">
    <a href="{{ route('employees.index') }}" class="text-decoration-none text-muted"><i class="bi bi-arrow-left"></i> {{ __('app.back_to_tasks') }}</a>
</div>

<div class="card border-0 shadow-sm" style="max-width: 600px;">
    <div class="card-body p-4 p-md-5">
        <h4 class="fw-bold mb-4">{{ isset($employee->id) ? __('app.edit') . ': ' . $employee->name : __('app.add_employee') }}</h4>

        <form action="{{ isset($employee->id) ? route('employees.update', $employee) : route('employees.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @if(isset($employee->id))
                @method('PUT')
            @endif

            <div class="mb-4 text-center">
                <div class="position-relative d-inline-block p-1 rounded-circle" style="background: linear-gradient(135deg, var(--primary-color), #818cf8);">
                    <img id="avatarPreview" src="{{ $employee->avatar_url ?? 'https://www.gravatar.com/avatar/00000000000000000000000000000000?d=mp&f=y' }}" class="rounded-circle shadow-sm" style="width: 120px; height: 120px; object-fit: cover; border: 4px solid var(--card-bg);">
                    <label for="avatar" class="position-absolute bottom-0 end-0 bg-primary text-white rounded-circle d-flex align-items-center justify-content-center shadow-lg border border-2 border-white" style="width: 36px; height: 36px; cursor: pointer; transform: translate(-10%, -10%);">
                        <i class="bi bi-camera-fill"></i>
                    </label>
                    <input type="file" name="avatar" id="avatar" class="d-none" accept="image/*" onchange="previewAvatar(event)">
                </div>
                @error('avatar') <div class="text-danger small mt-2">{{ $message }}</div> @enderror
            </div>

            <div class="mb-4">
                <label class="form-label fw-semibold text-muted small text-uppercase">{{ __('app.full_name') }}</label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $employee->name ?? '') }}" required>
                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="mb-4">
                <label class="form-label fw-semibold text-muted small text-uppercase">{{ __('app.email') }}</label>
                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $employee->email ?? '') }}" required>
                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            
            <div class="mb-4">
                <label class="form-label fw-semibold text-muted small text-uppercase">{{ __('app.role') }}</label>
                <select name="role" class="form-select @error('role') is-invalid @enderror" required>
                    <option value="">{{ __('app.select_role') }}</option>
                    <option value="{{ \App\Models\User::ROLE_SUPERADMIN }}" @selected(old('role', $employee->role ?? '') === \App\Models\User::ROLE_SUPERADMIN)>{{ __('app.role_superadmin') }}</option>
                    <option value="{{ \App\Models\User::ROLE_EMPLOYER }}" @selected(old('role', $employee->role ?? '') === \App\Models\User::ROLE_EMPLOYER)>{{ __('app.role_employer') }}</option>
                </select>
                @error('role') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="mb-4">
                <label class="form-label fw-semibold text-muted small text-uppercase">{{ __('app.password') }}</label>
                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" {{ isset($employee->id) ? '' : 'required' }}>
                @if(isset($employee->id))
                    <div class="form-text">{{ __('app.leave_blank') }}</div>
                @endif
                @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="mb-5">
                <label class="form-label fw-semibold text-muted small text-uppercase">{{ __('app.telegram_id') }}</label>
                <input type="text" name="telegram_id" class="form-control @error('telegram_id') is-invalid @enderror" value="{{ old('telegram_id', $employee->telegram_id ?? '') }}">
                <div class="form-text">{{ __('app.telegram_hint') }}</div>
                @error('telegram_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="d-flex justify-content-end gap-3">
                <a href="{{ route('employees.index') }}" class="btn btn-light border-0 px-4">{{ __('app.cancel') }}</a>
                <button type="submit" class="btn btn-primary px-4 shadow-sm">{{ isset($employee->id) ? __('app.save_changes') : __('app.save') }}</button>
            </div>
        </form>
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
            preview.style.display = "block";
        }
    }
</script>
@endsection