<?php

namespace App\Http\Controllers;

use App\Models\Type;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * Display a listing of users with search and filters for admin management.
     */
    public function index(Request $request): View
    {
        $search = $request->query('search');
        $typeId = $request->query('type_id');

        $query = User::with('type')->withCount('createdBooks')->orderBy('id', 'desc');

        if (! empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if (! empty($typeId)) {
            $query->where('type_id', $typeId);
        }

        $users = $query->paginate(15)->withQueryString();
        $types = Type::all();

        return view('admin.pages.users.index', [
            'users' => $users,
            'types' => $types,
            'filters' => [
                'search' => $search,
                'type_id' => $typeId,
            ],
        ]);
    }

    /**
     * Show form to create a new user account.
     */
    public function create(): View
    {
        $types = Type::all();

        return view('admin.pages.users.create', compact('types'));
    }

    /**
     * Store a newly created user account in database.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'type_id' => ['required', 'exists:types,id'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'type_id' => $validated['type_id'],
        ]);

        return redirect()->route('admin.users.index')->with('success', "Le compte de {$user->name} a été créé avec succès.");
    }

    /**
     * Show form to edit an existing user account.
     */
    public function edit(User $user): View
    {
        $types = Type::all();

        return view('admin.pages.users.edit', compact('user', 'types'));
    }

    /**
     * Update user details and role.
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'type_id' => ['required', 'exists:types,id'],
        ]);

        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'type_id' => $validated['type_id'],
        ];

        if (! empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }

        $user->update($data);

        return redirect()->route('admin.users.index')->with('success', "Le compte de {$user->name} a été mis à jour avec succès.");
    }

    /**
     * Quick update user role from inline dropdown.
     */
    public function updateRole(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'type_id' => ['nullable', 'exists:types,id'],
            'role' => ['nullable', 'in:guest,auteur,gerant,admin'],
        ]);

        if (! empty($validated['type_id'])) {
            $typeId = (int) $validated['type_id'];
        } else {
            $roleMap = ['guest' => 1, 'auteur' => 2, 'gerant' => 3, 'admin' => 4];
            $typeId = $roleMap[$validated['role'] ?? 'guest'] ?? 1;
        }

        $user->update(['type_id' => $typeId]);

        return redirect()->route('admin.users.index')->with('success', "Le rôle de l'utilisateur {$user->name} a été mis à jour vers « {$user->role} ».");
    }

    /**
     * Delete user account.
     */
    public function destroy(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users.index')->with('error', 'Vous ne pouvez pas supprimer votre propre compte.');
        }

        $userName = $user->name;
        $user->delete();

        return redirect()->route('admin.users.index')->with('success', "Le compte de {$userName} a été supprimé.");
    }
}
