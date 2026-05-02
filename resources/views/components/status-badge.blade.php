@props(['status'])

@php
    $classes = [
        'todo' => 'bg-slate-100 text-slate-700 ring-slate-200',
        'in_progress' => 'bg-sky-50 text-sky-700 ring-sky-200',
        'testing' => 'bg-violet-50 text-violet-700 ring-violet-200',
        'done' => 'bg-amber-50 text-amber-800 ring-amber-200',
        'complete' => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
    ][$status] ?? 'bg-zinc-100 text-zinc-700 ring-zinc-200';
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center rounded-md px-2 py-1 text-xs font-semibold ring-1 ring-inset {$classes}"]) }}>
    {{ __("app.statuses.{$status}") }}
</span>
