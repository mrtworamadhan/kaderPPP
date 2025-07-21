<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buat Agenda Baru</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; margin: 0; padding: 20px; background-color: #f0f2f5; }
        .container { max-width: 700px; margin: auto; background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
        h1 { text-align: center; color: #00573d; margin-bottom: 30px; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; font-weight: 600; color: #333; margin-bottom: 8px; }
        .form-group input, .form-group textarea, .form-group select {
            width: 100%; padding: 12px; font-size: 16px; border: 1px solid #ccd0d5;
            border-radius: 6px; box-sizing: border-box; transition: border-color 0.2s;
        }
        .form-group input:focus, .form-group textarea:focus { border-color: #00573d; outline: none; }
        .form-group textarea { resize: vertical; min-height: 100px; }
        .roles-container { max-height: 200px; overflow-y: auto; border: 1px solid #ccd0d5; border-radius: 6px; padding: 10px; }
        .checkbox-item { display: flex; align-items: center; margin-bottom: 8px; }
        .checkbox-item input { width: auto; margin-right: 10px; }
        .submit-btn {
            width: 100%; padding: 15px; font-size: 18px; font-weight: 600; color: #fff;
            background-color: #28a745; border: none; border-radius: 6px; cursor: pointer;
            transition: background-color 0.2s;
        }
        .submit-btn:hover { background-color: #218838; }
        .status-message { text-align: center; padding: 10px; border-radius: 6px; margin-top: 20px; font-weight: 500; }
        .status-success { background-color: #d4edda; color: #155724; }
        .status-error { background-color: #f8d7da; color: #721c24; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Buat Agenda Baru</h1>
        <form id="event-form">
            <div class="form-group">
                <label for="title">Judul Agenda</label>
                <input type="text" id="title" name="title" required>
            </div>
            <div class="form-group">
                <label for="description">Deskripsi</label>
                <textarea id="description" name="description"></textarea>
            </div>
            <div class="form-group">
                <label for="location">Lokasi</label>
                <input type="text" id="location" name="location" required>
            </div>
            <div class="form-group">
                <label for="start_time">Waktu Mulai</label>
                <input type="datetime-local" id="start_time" name="start_time" required>
            </div>
            <div class="form-group">
                <label for="end_time">Waktu Selesai</label>
                <input type="datetime-local" id="end_time" name="end_time" required>
            </div>
            <div class="form-group">
                <label for="points_reward">Poin Kehadiran</label>
                <input type="number" id="points_reward" name="points_reward" value="0" min="0" required>
            </div>
            <div class="form-group">
                <label>Target Peserta (Jabatan)</label>
                <div id="roles-container" class="roles-container">
                    <p>Memuat daftar jabatan...</p>
                </div>
            </div>
            <button type="submit" class="submit-btn">Simpan Agenda</button>
        </form>
        <div id="status-message" style="display: none;"></div>
    </div>

    <script>
        const API_ROLES_URL = '/api/events/roles';
        const API_STORE_EVENT_URL = '/api/events';
        const rolesContainer = document.getElementById('roles-container');
        const eventForm = document.getElementById('event-form');
        const statusMessage = document.getElementById('status-message');

        // PENTING: Ganti dengan token login admin yang valid saat testing
        const ADMIN_AUTH_TOKEN = '1|Qmrlr0ZrGsCTMUlRhUCLwr9BToWPsmE7NiZIJiq026a03c0c';

        // 1. Fungsi untuk memuat daftar jabatan/role dari API
        async function loadRoles() {
            try {
                const response = await fetch(API_ROLES_URL, {
                    headers: { 'Authorization': `Bearer ${ADMIN_AUTH_TOKEN}`, 'Accept': 'application/json' }
                });
                const result = await response.json();

                if (result.success) {
                    rolesContainer.innerHTML = ''; // Kosongkan container
                    result.data.forEach(role => {
                        const checkboxDiv = document.createElement('div');
                        checkboxDiv.className = 'checkbox-item';
                        checkboxDiv.innerHTML = `
                            <input type="checkbox" name="roles[]" value="${role.id}" id="role-${role.id}">
                            <label for="role-${role.id}">${role.name}</label>
                        `;
                        rolesContainer.appendChild(checkboxDiv);
                    });
                } else {
                    rolesContainer.innerHTML = '<p style="color: red;">Gagal memuat daftar jabatan.</p>';
                }
            } catch (error) {
                console.error("Error loading roles:", error);
                rolesContainer.innerHTML = '<p style="color: red;">Terjadi kesalahan jaringan.</p>';
            }
        }

        // 2. Fungsi untuk menangani submit form
        eventForm.addEventListener('submit', async function(event) {
            event.preventDefault(); // Mencegah form reload halaman
            statusMessage.style.display = 'none';
            
            // Mengumpulkan semua ID role yang dicentang
            const selectedRoles = [];
            document.querySelectorAll('input[name="roles[]"]:checked').forEach(checkbox => {
                selectedRoles.push(checkbox.value);
            });

            // Membuat body request
            const formData = {
                title: document.getElementById('title').value,
                description: document.getElementById('description').value,
                location: document.getElementById('location').value,
                start_time: document.getElementById('start_time').value,
                end_time: document.getElementById('end_time').value,
                points_reward: parseInt(document.getElementById('points_reward').value),
                roles: selectedRoles
            };

            try {
                const response = await fetch(API_STORE_EVENT_URL, {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${ADMIN_AUTH_TOKEN}`,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(formData)
                });

                const result = await response.json();

                if (response.ok && result.success) {
                    statusMessage.className = 'status-message status-success';
                    statusMessage.innerText = 'Agenda berhasil disimpan!';
                    eventForm.reset(); // Kosongkan form setelah berhasil
                } else {
                    // Menampilkan error validasi
                    let errorMessage = result.message || 'Gagal menyimpan agenda.';
                    if (result.errors) {
                        errorMessage += '\n' + Object.values(result.errors).flat().join('\n');
                    }
                    statusMessage.className = 'status-message status-error';
                    statusMessage.innerText = errorMessage;
                }
            } catch (error) {
                console.error("Error submitting form:", error);
                statusMessage.className = 'status-message status-error';
                statusMessage.innerText = 'Terjadi kesalahan jaringan.';
            } finally {
                statusMessage.style.display = 'block';
            }
        });

        // Panggil fungsi untuk memuat roles saat halaman dibuka
        document.addEventListener('DOMContentLoaded', loadRoles);
    </script>
</body>
</html>
