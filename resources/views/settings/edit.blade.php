@extends('layouts.app')

@section('title', __('app.settings'))

@section('content')
<div class="card border-0 shadow-sm" style="max-width: 600px;">
    <div class="card-body p-4 p-md-5">
        <h4 class="fw-bold mb-2">{{ __('app.telegram_settings') }}</h4>
        <p class="text-muted small mb-4">{{ __('app.telegram_settings_desc') }}</p>

        <form method="POST" action="{{ route('settings.update') }}">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label for="telegram_bot_token" class="form-label fw-semibold text-muted small text-uppercase">{{ __('app.bot_token') }}</label>
                <input type="text" 
                       class="form-control bg-light border-0 py-2" 
                       id="telegram_bot_token" 
                       name="telegram_bot_token" 
                       value="{{ old('telegram_bot_token', $settings['telegram_bot_token'] ?? '') }}" 
                       autocomplete="off">
            </div>

            <div class="mb-5">
                <label for="telegram_channel_id" class="form-label fw-semibold text-muted small text-uppercase">{{ __('app.channel_id') }}</label>
                <input type="text" 
                       class="form-control bg-light border-0 py-2" 
                       id="telegram_channel_id" 
                       name="telegram_channel_id" 
                       value="{{ old('telegram_channel_id', $settings['telegram_channel_id'] ?? '') }}" 
                       placeholder="@company_tasks or -1001234567890">
                <div class="form-text mt-2">{{ __('app.channel_hint') }}</div>
            </div>

            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-primary px-5 fw-semibold shadow-sm">{{ __('app.save') }}</button>
            </div>
        </form>
    </div>
</div>
@endsection