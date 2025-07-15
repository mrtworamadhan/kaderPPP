<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Kader Demokrat</title>
    <link rel="icon" type="image/png" href="/favicon-96x96.png" sizes="96x96" />
    <link rel="icon" type="image/svg+xml" href="/favicon.svg" />
    <link rel="shortcut icon" href="/favicon.ico" />
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png" />
    <meta name="apple-mobile-web-app-title" content="E-Kader Demokrat" />
    <link rel="manifest" href="/site.webmanifest" />

    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Optional: Custom Animation CSS (if needed) -->
    <style>
        .fade-in {
            animation: fadeIn 1s ease-in-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(30px); }
            to   { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="row align-items-center">
            <!-- Kiri: Teks -->
            <div class="col-lg-6 fade-in">
                <h1 class="fw-bold mb-4">Aplikasi E-Kader Demokrat</h1>
                <p class="lead">Platform inovasi DPC Demokrat Kab BOGOR untuk pendataan struktur partai, anggota, dan suara. Cocok untuk semua tingkatan dari DPC sampai KORT.</p>
                <p class="mb-4">Aplikasi ini membantu pengelolaan data anggota, input suara, pembentukan struktur DPAC dan DPRt, hingga akses berita untuk anggota melalui aplikasi mobile dan dashboard web.</p>
                <a href="https://play.google.com/store/apps/details?id=com.partai.app" class="btn btn-primary btn-lg rounded-pill px-4">Download Sekarang</a>
            </div>

            <!-- Kanan: Gambar -->
            <div class="col-lg-6 text-center fade-in">
                <img src="{{ asset('images/home.jpg') }}" alt="Aplikasi Partai" class="img-fluid rounded shadow" width="500">
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
