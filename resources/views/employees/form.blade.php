@csrf
<div class="grid gap-4 md:grid-cols-2">
    <div>
        <label class="text-sm font-medium text-zinc-800" for="name">{{ __('app.full_name') }}</label>
        <input class="mt-1.5 w-full rounded-md border border-zinc-300 px-3 py-2.5 text-sm shadow-sm outline-none transition focus:border-zinc-900 focus:ring-2 focus:ring-zinc-900/10" id="name" name="name" value="{{ old('name', $employee->name) }}" required>
    </div>
    <div>
        <label class="text-sm font-medium text-zinc-800" for="email">{{ __('app.email') }}</label>
        <input class="mt-1.5 w-full rounded-md border border-zinc-300 px-3 py-2.5 text-sm shadow-sm outline-none transition focus:border-zinc-900 focus:ring-2 focus:ring-zinc-900/10" id="email" name="email" type="email" value="{{ old('email', $employee->email) }}" required>
    </div>
    <div>
        <label class="text-sm font-medium text-zinc-800" for="password">{{ __('app.password') }}</label>
        <input class="mt-1.5 w-full rounded-md border border-zinc-300 px-3 py-2.5 text-sm shadow-sm outline-none transition focus:border-zinc-900 focus:ring-2 focus:ring-zinc-900/10" id="password" name="password" type="password" {{ $employee->exists ? '' : 'required' }}>
    </div>
    <div>
        <label class="text-sm font-medium text-zinc-800" for="telegram_id">{{ __('app.telegram_id') }}</label>
        <input class="mt-1.5 w-full rounded-md border border-zinc-300 px-3 py-2.5 text-sm shadow-sm outline-none transition focus:border-zinc-900 focus:ring-2 focus:ring-zinc-900/10" id="telegram_id" name="telegram_id" value="{{ old('telegram_id', $employee->telegram_id) }}">
    </div>
</div>
<div class="mt-6 flex gap-3">
    <button class="rounded-md bg-zinc-950 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-zinc-800">{{ __('app.save') }}</button>
    <a class="rounded-md border border-zinc-300 bg-white px-4 py-2.5 text-sm font-semibold text-zinc-700 transition hover:bg-zinc-50" href="{{ route('employees.index') }}">{{ __('app.cancel') }}</a>
</div>
