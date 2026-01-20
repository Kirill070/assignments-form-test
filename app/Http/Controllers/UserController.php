<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function create()
    {
        return view('user.create');
    }

    public function store(Request $request)
    {
        $respond = static fn (bool $success, string $message) => response()->json([
            'success' => $success,
            'message' => $message,
        ]);

        $validator = Validator::make($request->all(), [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'max:255',
                function (string $attribute, mixed $value, \Closure $fail): void {
                    if (!str_contains((string) $value, '@')) {
                        $fail('Email must contain "@".');
                    }
                },
            ],
            'password' => ['required', 'string'],
            'password_confirmation' => ['required', 'string', 'same:password'],
        ], [
            'first_name.required' => 'First name is required.',
            'last_name.required' => 'Last name is required.',
            'email.required' => 'Email is required.',
            'password.required' => 'Password is required.',
            'password_confirmation.required' => 'Password confirmation is required.',
            'password_confirmation.same' => 'Passwords do not match.',
        ]);

        if ($validator->fails()) {
            return $respond(false, $validator->errors()->first());
        }

        $data = $validator->validated();
        $email = trim($data['email']);

        $exists = User::where('email', $email)->exists();

        $status = $exists ? 'duplicate' : 'registered';
        $line = sprintf('[%s] email=%s status=%s' . "\n", now()->toDateTimeString(), $email, $status);

        if (!app()->environment('testing')) {
            file_put_contents(storage_path('logs/registration.log'), $line, FILE_APPEND);
        }

        if ($exists) {
            return $respond(false, 'User with this email already exists.');
        }

        User::create([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $email,
            'password' => Hash::make($data['password']),
        ]);

        return $respond(true, 'Registration successful.');
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
