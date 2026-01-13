<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Import Barang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            Import Barang dari Excel
        </div>

        <div class="card-body">

            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <form action="{{ route('import.barang') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="mb-3">
                    <label class="form-label">File Excel</label>
                    <input type="file" name="file" class="form-control" required>
                </div>

                <button class="btn btn-success">
                    Import Sekarang
                </button>
            </form>

            <hr>

            <small class="text-muted">
                Format Excel: <br>
                <b>kategori | sub kategori | kode barang | nama_barang</b>
            </small>

        </div>
    </div>
</div>

</body>
</html>
