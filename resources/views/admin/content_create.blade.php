<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Konten</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; margin: 0; padding: 20px; background-color: #f0f2f5; }
        .container { max-width: 800px; margin: auto; }
        .form-container { background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); margin-bottom: 30px; }
        h1, h2 { text-align: center; color: #00573d; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; font-weight: 600; color: #333; margin-bottom: 8px; }
        .form-group input, .form-group textarea, .form-group select { width: 100%; padding: 12px; font-size: 16px; border: 1px solid #ccd0d5; border-radius: 6px; box-sizing: border-box; }
        .form-group textarea { resize: vertical; min-height: 120px; }
        .submit-btn { width: 100%; padding: 15px; font-size: 18px; font-weight: 600; color: #fff; background-color: #28a745; border: none; border-radius: 6px; cursor: pointer; }
        .alert-success { padding: 15px; background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; border-radius: 6px; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Manajemen Konten</h1>

        @if (session('success'))
            <div class="alert-success">
                {{ session('success') }}
            </div>
        @endif

        <!-- Form untuk Arahan Ketua -->
        <div class="form-container">
            <h2>Buat Arahan Ketua Baru</h2>
            <form action="{{ route('admin.arahan.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="judul_arahan">Judul</label>
                    <input type="text" id="judul_arahan" name="judul" required>
                </div>
                <div class="form-group">
                    <label for="arahan">Isi Arahan</label>
                    <textarea id="arahan" name="arahan" required></textarea>
                </div>
                 <div class="form-group">
                    <label for="tanggal">Tanggal</label>
                    <input type="date" id="tanggal" name="tanggal" required>
                </div>
                <button type="submit" class="submit-btn">Simpan Arahan</button>
            </form>
        </div>

        <!-- Form untuk Kabar Terbaru -->
        <div class="form-container">
            <h2>Buat Kabar Terbaru Baru</h2>
            <form action="{{ route('admin.kabar.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label for="judul_kabar">Judul</label>
                    <input type="text" id="judul_kabar" name="judul" required>
                </div>
                <div class="form-group">
                    <label for="deskripsi">Deskripsi</label>
                    <textarea id="deskripsi" name="deskripsi"></textarea>
                </div>
                <div class="form-group">
                    <label for="foto">Upload Foto</label>
                    <input type="file" id="foto" name="foto" accept="image/*">
                </div>
                <div class="form-group">
                    <label for="status">Status</label>
                    <select id="status" name="status" required>
                        <option value="aktif">Aktif</option>
                        <option value="tidak aktif">Tidak Aktif</option>
                    </select>
                </div>
                <hr style="margin: 30px 0;">
                <h4>Fitur Affiliate (Opsional)</h4>
                <div class="form-group">
                    <label for="url_target">URL Target (Link Asli Medsos)</label>
                    <input type="url" id="url_target" name="url_target" placeholder="https://instagram.com/p/...">
                </div>
                <div class="form-group">
                    <label for="points_per_click">Poin per Klik</label>
                    <input type="number" id="points_per_click" name="points_per_click" value="0" min="0">
                </div>
                <div class="form-group">
                    <label for="share_expires_at">Poin Kedaluwarsa Pada</label>
                    <input type="datetime-local" id="share_expires_at" name="share_expires_at">
                </div>
                <button type="submit" class="submit-btn">Simpan Kabar Terbaru</button>
            </form>
        </div>
    </div>
</body>
</html>
