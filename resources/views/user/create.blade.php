<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/js/app.js'])
        @endif
    </head>
    <body class="bg-light">
        <div class="container py-5">
            <div class="row justify-content-center">
                <div class="col-lg-6">
                    <div class="card shadow-sm">
                        <div class="card-body p-4">
                            <h1 class="h4 mb-3">Регистрация</h1>

                            <div id="register-error" class="alert alert-danger d-none" role="alert"></div>
                            <div id="register-success" class="alert alert-success d-none" role="alert"></div>

                            <form id="registration-form" action="{{ route('users.store') }}" method="post" novalidate>
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label" for="first_name">Имя</label>
                                    <input class="form-control" type="text" id="first_name" name="first_name" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label" for="last_name">Фамилия</label>
                                    <input class="form-control" type="text" id="last_name" name="last_name" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label" for="email">Email</label>
                                    <input class="form-control" type="email" id="email" name="email" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label" for="password">Пароль</label>
                                    <input class="form-control" type="password" id="password" name="password" required>
                                </div>
                                <div class="mb-4">
                                    <label class="form-label" for="password_confirmation">Повтор пароля</label>
                                    <input class="form-control" type="password" id="password_confirmation" name="password_confirmation" required>
                                </div>
                                <button class="btn btn-primary w-100" type="submit">Зарегистрироваться</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
