<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * Display a listing of users for admin management.
     */
    public function index(): View
    {
        $users = User::orderBy('id', 'desc')->paginate(15);

        return view('admin.pages.users.index', compact('users'));
    }

    /**
     * Update user role.
     */
    public function updateRole(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'role' => ['required', 'in:guest,auteur,gerant,admin'],
        ]);

        $user->update(['role' => $validated['role']]);

        return redirect()->route('admin.users.index')->with('success', "Le rôle de l'utilisateur {$user->name} a été mis à jour vers « {$validated['role']} ».");
    }

    /**
     * Delete user account.
     */
    public function destroy(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users.index')->with('error', 'Vous ne pouvez pas supprimer votre propre compte.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')->with('success', "Le compte de {$user->name} a été supprimé.");
    }
}
