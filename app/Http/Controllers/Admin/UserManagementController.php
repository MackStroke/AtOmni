<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TeamMember;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Rule;

class UserManagementController extends Controller
{
    /**
     * Abort if current user is not super_admin.
     */
    private function gateSuperAdmin(): void
    {
        if (! auth()->user()->isSuperAdmin()) {
            abort(403, 'Only super administrators can manage team logins.');
        }
    }

    public function index(Request $request)
    {
        $this->gateSuperAdmin();

        $query = User::withTrashed()->with('teamMember');

        if ($request->has('sort')) {
            $dir = strtolower($request->input('dir', 'desc')) === 'asc' ? 'asc' : 'desc';
            $query->orderBy($request->sort, $dir);
        } else {
            $query->latest();
        }

        if ($role = $request->get('role')) {
            $query->where('role', $role);
        }

        if ($request->has('fetch_all_ids')) {
            return response()->json($query->pluck('id'));
        }

        $users   = $query->paginate(20)->withQueryString();
        $members = TeamMember::orderBy('name')->get();

        return view('admin.users.index', compact('users', 'members'));
    }

    public function create()
    {
        $this->gateSuperAdmin();

        $members = TeamMember::orderBy('name')->get();

        return view('admin.users.create', compact('members'));
    }

    public function store(Request $request)
    {
        $this->gateSuperAdmin();

        $validated = $request->validate([
            'name'             => 'required|string|max:255',
            'email'            => 'required|email|unique:users,email',
            'role'             => 'required|string|in:editor,author,contributor',
            'password'         => ['required', 'confirmed', Password::min(8)->letters()->numbers()],
            'bio'              => 'nullable|string|max:1000',
            'team_member_id'   => 'nullable|exists:team_members,id',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        User::create($validated);

        return redirect()
            ->route('admin.users.index')
            ->with('success', "Login created for {$validated['name']} ({$validated['role']}).");
    }

    public function edit(User $user)
    {
        $this->gateSuperAdmin();

        $members = TeamMember::orderBy('name')->get();

        return view('admin.users.edit', compact('user', 'members'));
    }

    public function update(Request $request, User $user)
    {
        $this->gateSuperAdmin();

        $validated = $request->validate([
            'name'           => 'required|string|max:255',
            'email'          => ['required', 'email', Rule::unique('users', 'email')->ignore($user->id)],
            'role'           => 'required|string|in:super_admin,editor,author,contributor',
            'bio'            => 'nullable|string|max:1000',
            'team_member_id' => 'nullable|exists:team_members,id',
            'password'       => ['nullable', 'confirmed', Password::min(8)->letters()->numbers()],
        ]);

        // Prevent demoting yourself from super_admin
        if ($user->id === auth()->id() && $validated['role'] !== 'super_admin') {
            return back()->withErrors(['role' => 'You cannot demote your own super_admin account.'])->withInput();
        }

        if (! empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        return redirect()
            ->route('admin.users.index')
            ->with('success', "Account for {$user->name} updated successfully.");
    }

    public function destroy(User $user)
    {
        $this->gateSuperAdmin();

        if ($user->id === auth()->id()) {
            return back()->withErrors(['general' => 'You cannot delete your own account.']);
        }

        $user->delete(); // soft delete

        return redirect()
            ->route('admin.users.index')
            ->with('success', "{$user->name}'s login has been revoked (soft-deleted).");
    }

    public function restore(int $id)
    {
        $this->gateSuperAdmin();

        $user = User::withTrashed()->findOrFail($id);
        $user->restore();

        return redirect()
            ->route('admin.users.index')
            ->with('success', "{$user->name}'s login has been restored.");
    }

    public function resetPassword(Request $request, User $user)
    {
        $this->gateSuperAdmin();

        $validated = $request->validate([
            'password' => ['required', 'confirmed', Password::min(8)->letters()->numbers()],
        ]);

        $user->update(['password' => Hash::make($validated['password'])]);

        return redirect()
            ->route('admin.users.index')
            ->with('success', "Password for {$user->name} has been reset.");
    }
}
