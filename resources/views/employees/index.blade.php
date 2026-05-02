@extends('layouts.app')

@section('title', __('app.employees'))

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">{{ __('app.employee_list') }}</h4>
    </div>
    <a href="{{ route('employees.create') }}" class="btn btn-primary shadow-sm">
        <i class="bi bi-person-plus me-2"></i> {{ __('app.add_employee') }}
    </a>
</div>

<div class="card overflow-hidden">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th class="px-4">{{ __('app.full_name') }}</th>
                    <th>{{ __('app.email') }}</th>
                    <th>{{ __('app.telegram_id') }}</th>
                    <th>{{ __('app.joined_at') }}</th>
                    <th class="text-end px-4">{{ __('app.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($employees as $employee)
                <tr>
                    <td class="px-4 py-3">
                        <div class="d-flex align-items-center gap-3">
                            <img src="{{ $employee->avatar_url }}" alt="{{ $employee->name }}" class="rounded-circle shadow-sm border" style="width: 40px; height: 40px; object-fit: cover;">
                            <span class="fw-semibold text-body">{{ $employee->name }}</span>
                        </div>
                    </td>
                    <td class="text-muted">{{ $employee->email }}</td>
                    <td class="text-muted">{{ $employee->telegram_id ?: '-' }}</td>
                    <td class="text-muted small">{{ $employee->created_at->format('d M Y') }}</td>
                    <td class="text-end px-4">
                        <a href="{{ route('employees.edit', $employee) }}" class="btn btn-sm btn-light border-0 text-primary" title="{{ __('app.edit') }}"><i class="bi bi-pencil"></i></a>
                        <form action="{{ route('employees.destroy', $employee) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('app.delete_confirm') }}');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-light border-0 text-danger" title="{{ __('app.delete') }}"><i class="bi bi-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4">
    {{ $employees->links('pagination::bootstrap-5') }}
</div>
@endsection