@props(['priority'])

@php
    $classes = [
        'low' => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
        'medium' => 'bg-amber-50 text-amber-800 ring-amber-200',
        'high' => 'bg-rose-50 text-rose-700 ring-rose-200',
    ][$priority] ?? 'bg-zinc-100 text-zinc-700 ring-zinc-200';
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center rounded-md px-2 py-1 text-xs font-semibold ring-1 ring-inset {$classes}"]) }}>
    {{ __("app.priorities.{$priority}") }}
</span>
