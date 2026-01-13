<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Import Laba</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="card shadow">
        <div class="card-header bg-success text-white">
            Import Laba (Modal → Harga Jual)
        </div>

        <div class="card-body">

            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('laba.import') }}" method="POST" enctype="multipart/form-data">
                @csrf

                {{-- AREA --}}
                <div class="mb-3">
                    <label class="form-label">Area</label>
                    <select name="id_area" class="form-select" required>
                        <option value="">-- Pilih Area --</option>
                        @foreach($areas as $area)
                            <option value="{{ $area->id }}">
                                {{ $area->area }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- FILE --}}
                <div class="mb-3">
                    <label class="form-label">File Excel</label>
                    <input type="file" name="file" class="form-control" required>
                </div>

                <button class="btn btn-success">
                    Import Laba
                </button>
            </form>

            <hr>

            <div class="alert alert-info">
                <strong>Format Excel (WAJIB):</strong>
                <pre class="mb-0">
modal | harga
100   | 150
110   | 200
120   | 200
130   | 200
...
                </pre>
                <small>
                    Sistem akan otomatis:
                    <ul class="mb-0">
                        <li>Group berdasarkan <b>harga</b></li>
                        <li>Ambil <b>modal terkecil</b> → input_minimal</li>
                        <li>Ambil <b>modal terbesar</b> → input_maksimal</li>
                    </ul>
                </small>
            </div>

        </div>
    </div>
</div>

</body>
</html>
