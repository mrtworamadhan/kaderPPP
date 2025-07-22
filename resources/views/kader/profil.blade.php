<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Saya</title>
    <style>
        body, html { margin: 0; padding: 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #f0f2f5; color: #333; }
        .container { padding: 15px; max-width: 800px; margin: auto; }
        .header { padding: 10px 15px; background-color: #00573d; color: white; font-size: 20px; font-weight: 600; }
        .profile-card { background: #fff; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); padding: 25px; }
        .profile-header { text-align: center; margin-bottom: 25px; }
        .profile-photo-wrapper { position: relative; width: 120px; height: 120px; margin: 0 auto 15px; }
        .profile-photo { width: 100%; height: 100%; border-radius: 50%; object-fit: cover; border: 4px solid #fff; box-shadow: 0 2px 8px rgba(0,0,0,0.15); }
        .upload-btn {
            position: absolute; bottom: 5px; right: 5px; background-color: #00573d; color: white;
            border: 2px solid white; border-radius: 50%; width: 32px; height: 32px;
            display: flex; align-items: center; justify-content: center; cursor: pointer;
        }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; font-weight: 600; color: #555; margin-bottom: 8px; font-size: 14px; }
        .form-group input {
            width: 100%; padding: 12px; font-size: 16px; border: 1px solid #ccd0d5;
            border-radius: 6px; box-sizing: border-box;
        }
        .form-group input[readonly] { background-color: #e9ebee; cursor: not-allowed; }
        .submit-btn { width: 100%; background-color: #28a745; color: white; border: none; padding: 15px; border-radius: 8px; font-size: 16px; font-weight: 600; cursor: pointer; }
        .status-message { text-align: center; padding: 10px; border-radius: 6px; margin-top: 20px; font-weight: 500; }
        .status-success { background-color: #d4edda; color: #155724; }
        .status-error { background-color: #f8d7da; color: #721c24; }
    </style>
</head>
<body>
    <div class="header">Profil Saya</div>
    <div class="container">
        <div id="loading-indicator" style="text-align: center; padding: 40px;">Memuat profil...</div>
        <div id="profile-container" class="profile-card" style="display: none;">
            <form id="profile-form">
                <div class="profile-header">
                    <div class="profile-photo-wrapper">
                        <img id="profile-photo-preview" src="https://via.placeholder.com/120" alt="Foto Profil" class="profile-photo">
                        <label for="photo-upload" class="upload-btn">
                            <svg xmlns="http://www.w3.org/2000/svg" height="16" width="16" viewBox="0 0 512 512" fill="white"><path d="M149.1 64.8L138.7 96H64C28.7 96 0 124.7 0 160V416c0 35.3 28.7 64 64 64H448c35.3 0 64-28.7 64-64V160c0-35.3-28.7-64-64-64H373.3L362.9 64.8C356.4 45.2 338.1 32 317.4 32H194.6c-20.7 0-39 13.2-45.5 32.8zM256 192a96 96 0 1 1 0 192 96 96 0 1 1 0-192z"/></svg>
                        </label>
                        <input type="file" id="photo-upload" name="foto" accept="image/*" style="display: none;">
                    </div>
                </div>
                <div class="form-group">
                    <label for="nik">NIK</label>
                    <input type="text" id="nik" name="nik" readonly>
                </div>
                <div class="form-group">
                    <label for="nama">Nama Lengkap</label>
                    <input type="text" id="nama" name="nama" required>
                </div>
                <div class="form-group">
                    <label for="phone">No. HP</label>
                    <input type="tel" id="phone" name="phone">
                </div>
                <div class="form-group">
                    <label for="tgl_lahir">Tanggal Lahir</label>
                    <input type="date" id="tgl_lahir" name="tgl_lahir">
                </div>
                <div class="form-group">
                    <label for="pekerjaan">Pekerjaan</label>
                    <input type="text" id="pekerjaan" name="pekerjaan">
                </div>
                <div class="form-group">
                    <label for="alamat">Alamat</label>
                    <input type="text" id="alamat" name="alamat">
                </div>
                <button type="submit" class="submit-btn">Simpan Perubahan</button>
            </form>
            <div id="status-message" style="display: none;"></div>
        </div>
    </div>

    <script>
        const API_URL = '/api/kader/profil';
        const LOADING_INDICATOR = document.getElementById('loading-indicator');
        const PROFILE_CONTAINER = document.getElementById('profile-container');
        const PROFILE_FORM = document.getElementById('profile-form');
        const PHOTO_UPLOAD = document.getElementById('photo-upload');
        const PHOTO_PREVIEW = document.getElementById('profile-photo-preview');
        const STATUS_MESSAGE = document.getElementById('status-message');
        
        let authToken = null;

        async function loadProfile() {
            try {
                const response = await fetch(API_URL, {
                    headers: { 'Authorization': `Bearer ${authToken}`, 'Accept': 'application/json' }
                });
                if (!response.ok) throw new Error('Gagal memuat profil');
                const result = await response.json();
                if (result.success) {
                    const anggota = result.data;
                    document.getElementById('nik').value = anggota.nik;
                    document.getElementById('nama').value = anggota.nama;
                    document.getElementById('phone').value = anggota.phone;
                    document.getElementById('tgl_lahir').value = anggota.tgl_lahir;
                    document.getElementById('pekerjaan').value = anggota.pekerjaan;
                    document.getElementById('alamat').value = anggota.alamat;
                    PHOTO_PREVIEW.src = anggota.foto ? `/storage/${anggota.foto}` : 'https://via.placeholder.com/120';
                    
                    LOADING_INDICATOR.style.display = 'none';
                    PROFILE_CONTAINER.style.display = 'block';
                }
            } catch (error) {
                LOADING_INDICATOR.innerText = 'Gagal memuat profil.';
                console.error("Error:", error);
            }
        }

        PHOTO_UPLOAD.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    PHOTO_PREVIEW.src = e.target.result;
                }
                reader.readAsDataURL(file);
            }
        });

        PROFILE_FORM.addEventListener('submit', async function(e) {
            e.preventDefault();
            STATUS_MESSAGE.style.display = 'none';
            const formData = new FormData(this);

            try {
                const response = await fetch(API_URL, {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${authToken}`,
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                const result = await response.json();
                if (result.success) {
                    STATUS_MESSAGE.className = 'status-message status-success';
                    STATUS_MESSAGE.innerText = 'Profil berhasil diperbarui!';
                } else {
                    let errorMsg = result.message || 'Gagal memperbarui profil.';
                    if(result.errors) errorMsg += ' ' + Object.values(result.errors).flat().join(', ');
                    STATUS_MESSAGE.className = 'status-message status-error';
                    STATUS_MESSAGE.innerText = errorMsg;
                }
            } catch (error) {
                STATUS_MESSAGE.className = 'status-message status-error';
                STATUS_MESSAGE.innerText = 'Terjadi kesalahan jaringan.';
            } finally {
                STATUS_MESSAGE.style.display = 'block';
            }
        });

        function start(token) {
            authToken = token;
            loadProfile();
        }

        document.addEventListener('DOMContentLoaded', () => {
             const TEST_TOKEN = '6|yuAL1icCCjvNkoOZhZ4L2kit07ixxHK3sUO8g1Ccc00ef44c';
             start(TEST_TOKEN);
        });
    </script>
</body>
</html>
