<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container d-flex align-items-center justify-content-center vh-100">
    <div class="col-12 col-md-5">
        <div class="card shadow-lg border-0">
            <div class="card-body p-4">
                <h3 class="text-center mb-3 fw-bold">Lupa Password</h3>
                <p class="text-muted text-center mb-4">Masukkan email Anda untuk mendapatkan link reset password.</p>

                @if (session('status'))
                    <div class="alert alert-success">
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('password.email') }}">
                    @csrf
                    <div class="mb-3">
                        <label for="email" class="form-label">Alamat Email</label>
                        <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror"
                               value="{{ old('email') }}" required autofocus>
                        @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary w-100">Kirim Link Reset</button>
                </form>

                <div class="mt-3 text-center">
                    <a href="{{ route('login') }}">Kembali ke Login</a>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
