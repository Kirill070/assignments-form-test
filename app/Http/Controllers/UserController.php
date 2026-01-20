<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function create()
    {
        return view('user.create');
    }

    public function store(Request $request)
    {
        $firstName = trim((string) $request->input('first_name'));
        $lastName = trim((string) $request->input('last_name'));
        $email = trim((string) $request->input('email'));
        $password = (string) $request->input('password');
        $passwordConfirmation = (string) $request->input('password_confirmation');

        if ($firstName === '') {
            return response()->json([
                'success' => false,
                'message' => 'First name is required.',
            ]);
        }

        if ($lastName === '') {
            return response()->json([
                'success' => false,
                'message' => 'Last name is required.',
            ]);
        }

        if ($email === '') {
            return response()->json([
                'success' => false,
                'message' => 'Email is required.',
            ]);
        }

        if (!str_contains($email, '@')) {
            return response()->json([
                'success' => false,
                'message' => 'Email must contain "@".',
            ]);
        }

        if ($password === '') {
            return response()->json([
                'success' => false,
                'message' => 'Password is required.',
            ]);
        }

        if ($password !== $passwordConfirmation) {
            return response()->json([
                'success' => false,
                'message' => 'Passwords do not match.',
            ]);
        }

        $exists = User::where('email', $email)->exists();

        $status = $exists ? 'duplicate' : 'registered';
        $line = sprintf('[%s] email=%s status=%s' . "\n", now()->toDateTimeString(), $email, $status);

        if (!app()->environment('testing')) {
            file_put_contents(storage_path('logs/registration.log'), $line, FILE_APPEND);
        }

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'User with this email already exists.',
            ]);
        }

        User::create([
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $email,
            'password' => Hash::make($password),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Registration successful.',
        ]);
    }

    public function index()
    {
        return response()->json(User::all());
    }

    public function show(User $user)
    {
        return response()->json($user);
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'first_name' => ['sometimes', 'required', 'string', 'max:255'],
            'last_name' => ['sometimes', 'required', 'string', 'max:255'],
            'email' => ['sometimes', 'required', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'password' => ['sometimes', 'required', 'string', 'min:6'],
        ]);

        $user->update($data);

        return response()->json($user);
    }

    public function destroy(User $user)
    {
        $user->delete();

        return response()->json(null, 204);
    }
}
