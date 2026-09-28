<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        $users = User::withCount(['deceaseds', 'payments'])
            ->orderBy('name')
            ->get();

        return view('admin.users', compact('users'));
    }

    public function updateRole(Request $request, User $user)
    {
        $request->validate([
            'role' => ['required', Rule::in(array_keys(User::ROLES))],
        ]);

        if ($user->is($request->user()) && $request->role !== User::ROLE_ADMIN) {
            return back()->with('error', 'You cannot remove your own administrator role.');
        }

        $user->role = $request->role;
        $user->save();

        return back()->with('success', "{$user->name} is now {$user->roleLabel()}.");
    }
}
