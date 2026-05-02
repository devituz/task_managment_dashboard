<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $query = User::latest();

        $query->when($request->filled('search'), function ($query) use ($request) {
            $search = '%' . $request->string('search')->toString() . '%';
            $query->where(function ($query) use ($search) {
                $query->where('name', 'like', $search)
                      ->orWhere('email', 'like', $search);
            });
        });

        return view('employees.index', [
            'employees' => $query->paginate(12)->withQueryString(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('employees.create', ['employee' => new User(['role' => User::ROLE_EMPLOYER])]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'],
            'telegram_id' => ['nullable', 'string', 'max:255'],
            'role' => ['required', Rule::in([User::ROLE_SUPERADMIN, User::ROLE_EMPLOYER])],
            'avatar' => ['nullable', 'image', 'max:2048'],
        ]);

        $data['password'] = Hash::make($data['password']);

        if ($request->hasFile('avatar')) {
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        User::create($data);

        return redirect()->route('employees.index')->with('success', __('app.task_created') ?? 'Employee created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return redirect()->route('employees.edit', $id);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $employee): View
    {
        return view('employees.edit', compact('employee'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $employee): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($employee->id)],
            'password' => ['nullable', 'string', 'min:6'],
            'telegram_id' => ['nullable', 'string', 'max:255'],
            'role' => ['required', Rule::in([User::ROLE_SUPERADMIN, User::ROLE_EMPLOYER])],
            'avatar' => ['nullable', 'image', 'max:2048'],
        ]);

        if (blank($data['password'])) {
            unset($data['password']);
        } else {
            $data['password'] = Hash::make($data['password']);
        }

        if ($request->hasFile('avatar')) {
            if ($employee->avatar) {
                Storage::disk('public')->delete($employee->avatar);
            }
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $employee->update($data);

        return redirect()->route('employees.index')->with('success', __('app.profile_updated') ?? 'Employee updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $employee): RedirectResponse
    {
        if ($employee->id === auth()->id()) {
            return back()->with('error', 'Siz o\'zingizni o\'chira olmaysiz.');
        }

        if ($employee->avatar) {
            Storage::disk('public')->delete($employee->avatar);
        }

        $employee->delete();

        return redirect()->route('employees.index')->with('success', __('app.task_deleted') ?? 'Employee deleted.');
    }
}
