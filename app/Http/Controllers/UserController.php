<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Mechanic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (auth()->user()->role !== 'admin') {
                abort(403);
            }
            return $next($request);
        });
    }

    public function index()
    {
        $data = User::with('mechanic')->latest()->get();
        return view('user.index', compact('data'));
    }

    public function create()
    {
        $mechanics = Mechanic::whereNull('user_id')->orderBy('name')->get();
        return view('user.form', compact('mechanics'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'username' => 'required|max:50|unique:users,username',
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|min:6',
            'role' => 'required|in:admin,kasir,mekanik',
            'mechanic_id' => 'nullable|exists:mechanics,id',
        ]);

        $user = User::create(['password' => Hash::make($request->password), 'role' => $request->role] + $request->only(['username', 'name', 'email']));

        if ($request->role === 'mekanik') {
            if ($request->mechanic_id) {
                Mechanic::find($request->mechanic_id)->update(['user_id' => $user->id]);
            } else {
                Mechanic::create(['name' => $request->name, 'user_id' => $user->id, 'status' => 'active']);
            }
        }

        return redirect()->route('users.index')->with('success', 'User berhasil ditambahkan.');
    }

    public function edit(User $user)
    {
        $mechanics = Mechanic::whereNull('user_id')->orWhere('user_id', $user->id)->orderBy('name')->get();
        return view('user.form', compact('user', 'mechanics'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'username' => 'required|max:50|unique:users,username,' . $user->id,
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|min:6',
            'role' => 'required|in:admin,kasir,mekanik',
            'mechanic_id' => 'nullable|exists:mechanics,id',
        ]);

        $data = $request->only(['username', 'name', 'email', 'role']);
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        $currentMechanic = $user->mechanic;

        if ($request->role === 'mekanik') {
            if ($request->mechanic_id) {
                if ($currentMechanic && $currentMechanic->id != $request->mechanic_id) {
                    $currentMechanic->update(['user_id' => null]);
                }
                Mechanic::find($request->mechanic_id)->update(['user_id' => $user->id]);
            } elseif (!$currentMechanic) {
                Mechanic::create(['name' => $request->name, 'user_id' => $user->id, 'status' => 'active']);
            }
        } elseif ($currentMechanic) {
            $currentMechanic->update(['user_id' => null]);
        }

        return redirect()->route('users.index')->with('success', 'User berhasil diubah.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Tidak bisa menghapus akun sendiri.');
        }
        $user->delete();
        return redirect()->route('users.index')->with('success', 'User berhasil dihapus.');
    }
}
