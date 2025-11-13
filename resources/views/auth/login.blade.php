<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'POS System') }} - Login</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Tailwind CSS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="font-sans text-gray-900 antialiased bg-gradient-to-br from-indigo-50 to-blue-100 min-h-screen">
    <div class="min-vh-100 d-flex flex-column align-items-center justify-content-center py-6">
        <div class="col-12 col-sm-8 col-md-6 col-lg-4">
            <div class="card shadow-sm border-0 rounded-3 overflow-hidden">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <div class="mx-auto bg-indigo-100 text-indigo-600 w-16 h-16 rounded-full d-flex align-items-center justify-content-center mb-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                            </svg>
                        </div>
                        <h2 class="h4 fw-bold text-gray-800 mb-1">POS System</h2>
                        <p class="text-gray-600 mb-0">Sign in to your account</p>
                    </div>

                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="email" class="form-label fw-medium text-gray-700">Email Address</label>
                            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                            @error('email')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label fw-medium text-gray-700">Password</label>
                            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">
                            @error('password')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="mb-3 form-check">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                            <label class="form-check-label text-gray-700" for="remember">
                                Remember Me
                            </label>
                        </div>

                        <div class="d-grid mb-3">
                            <button type="submit" class="btn btn-primary py-2">
                                Sign in
                            </button>
                        </div>
                    </form>

                    <div class="text-center">
                        @if (Route::has('password.request'))
                            <a class="text-sm text-indigo-600 text-decoration-none" href="{{ route('password.request') }}">
                                Forgot your password?
                            </a>
                        @endif
                    </div>
                </div>
            </div>
            
            <div class="text-center mt-4">
                <p class="text-sm text-gray-600">&copy; {{ date('Y') }} {{ config('app.name', 'POS System') }}. All rights reserved.</p>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('password') border-red-300 @enderror">
                </div>

                <div class="mb-4 flex items-center">
                    <input id="remember_me" type="checkbox"
                        class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                    <label for="remember_me" class="ml-2 block text-sm text-gray-900">Remember me</label>
                </div>

                <div class="flex items-center justify-between mt-6">
                    <button type="submit"
                        class="w-full bg-indigo-600 text-white py-2 px-4 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Sign in
                    </button>
                </div>
            </form>

            <div class="mt-6 text-center">
                <a href="#" class="text-sm text-indigo-600 hover:text-indigo-500">
                    Forgot your password?
                </a>
            </div>
        </div>

        <div class="mt-8 text-center text-sm text-gray-600">
            <p>&copy; {{ date('Y') }} {{ config('app.name', 'POS System') }}. All rights reserved.</p>
        </div>
    </div>
</body>

</html>
