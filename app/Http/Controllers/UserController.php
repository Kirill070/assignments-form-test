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
        $email = trim((string) $request->input('email'));
        $password = (string) $request->input('password');
        $passwordConfirmation = (string) $request->input('password_confirmation');

        if ($email === '' || !str_contains($email, '@')) {
            return response()->json([
                'success' => false,
                'message' => 'Email must contain "@".',
            ]);
        }

        if ($password === '' || $password !== $passwordConfirmation) {
            return response()->json([
                'success' => false,
                'message' => 'Passwords do not match.',
            ]);
        }

        $existingUsers = [
            ['id' => 1, 'name' => 'Ivan Petrov', 'email' => 'ivan.petrov@example.test'],
            ['id' => 2, 'name' => 'Maria Ivanova', 'email' => 'maria.ivanova@example.test'],
            ['id' => 3, 'name' => 'Sergey Smirnov', 'email' => 'sergey.smirnov@example.test'],
        ];

        $exists = collect($existingUsers)->contains(function (array $user) use ($email) {
            return $user['email'] === $email;
        });

        $status = $exists ? 'duplicate' : 'registered';
        $line = sprintf(
            "[%s] email=%s status=%s\n",
            now()->toDateTimeString(),
            $email,
            $status
        );

        file_put_contents(storage_path('logs/registration.log'), $line, FILE_APPEND);

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'User with this email already exists.',
            ]);
        }

        User::create([
            'first_name' => trim((string) $request->input('first_name')),
            'last_name' => trim((string) $request->input('last_name')),
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
