<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekapitulasi Agenda</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; margin: 0; padding: 20px; background-color: #f0f2f5; }
        .container { max-width: 1200px; margin: auto; }
        h1 { text-align: center; color: #00573d; margin-bottom: 30px; }
        h2 { font-size: 20px; color: #00573d; margin-bottom: 15px; border-left: 4px solid #f9a825; padding-left: 10px; }
        .table-wrapper { overflow-x: auto; background: #fff; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); margin-bottom: 30px; }
        table { width: 100%; border-collapse: collapse; white-space: nowrap; }
        th, td { padding: 12px 15px; text-align: left; border-bottom: 1px solid #e9ebee; }
        th { background-color: #f5f6f7; font-size: 14px; color: #606770; }
        td { font-size: 14px; vertical-align: middle; }
        .progress-bar-container { width: 100px; background-color: #e9ebee; border-radius: 5px; height: 10px; overflow: hidden; }
        .progress-bar { background-color: #28a745; height: 100%; border-radius: 5px; }
        .actions a {
            display: inline-block; padding: 6px 12px; margin-right: 5px; border-radius: 5px;
            color: #fff; text-decoration: none; font-size: 12px; font-weight: 500;
        }
        .btn-qr { background-color: #17a2b8; }
        .btn-attendees { background-color: #007bff; }
        .info-kosong { text-align: center; color: #888; padding: 30px; }
        #loading-indicator { text-align: center; padding: 40px; font-size: 16px; color: #606770; }

        /* --- CSS BARU UNTUK MODAL --- */
        .modal-overlay {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0,0,0,0.6); display: none; justify-content: center;
            align-items: center; z-index: 1000;
        }
        .modal-content {
            background: #fff; padding: 25px; border-radius: 8px;
            width: 90%; max-width: 600px; max-height: 80vh; overflow-y: auto;
            position: relative;
        }
        .modal-close {
            position: absolute; top: 15px; right: 15px; font-size: 24px;
            font-weight: bold; cursor: pointer; color: #888;
        }
        .modal-header { margin-bottom: 20px; }
        .modal-header h3 { margin: 0; color: #00573d; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Rekapitulasi Agenda</h1>
        <!-- ... (Tabel Agenda) ... -->
        <div id="akan-datang-container">
            <h2>Akan Datang</h2>
            <div class="table-wrapper">
                <table id="table-akan-datang">
                    <thead>
                        <tr>
                            <th>Judul Agenda</th>
                            <th>Waktu Mulai</th>
                            <th>Lokasi</th>
                            <th>Target Peserta</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
        <div id="berlangsung-container">
            <h2>Sedang Berlangsung</h2>
            <div class="table-wrapper">
                <table id="table-berlangsung">
                    <thead>
                        <tr>
                            <th>Judul Agenda</th>
                            <th>Waktu Berakhir</th>
                            <th>Lokasi</th>
                            <th>Kehadiran</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
        <div id="selesai-container">
            <h2>Selesai</h2>
            <div class="table-wrapper">
                <table id="table-selesai">
                    <thead>
                        <tr>
                            <th>Judul Agenda</th>
                            <th>Tanggal</th>
                            <th>Lokasi</th>
                            <th>Kehadiran</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
        <div id="loading-indicator">Memuat data...</div>
    </div>

    <!-- --- HTML BARU UNTUK MODAL DAFTAR HADIR --- -->
    <div id="attendees-modal" class="modal-overlay">
        <div class="modal-content">
            <span class="modal-close" onclick="closeModal()">&times;</span>
            <div class="modal-header">
                <h3 id="modal-title">Daftar Peserta Hadir</h3>
            </div>
            <div class="table-wrapper">
                <table id="attendees-table">
                    <thead>
                        <tr>
                            <th>NIK</th>
                            <th>Nama</th>
                            <th>No. HP</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Daftar peserta akan dimuat di sini -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        const API_URL = '/api/events/admin-index';
        const ADMIN_AUTH_TOKEN = '1|Qmrlr0ZrGsCTMUlRhUCLwr9BToWPsmE7NiZIJiq026a03c0c';

        function formatDate(dateString) {
            const options = { year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' };
            return new Date(dateString).toLocaleString('id-ID', options);
        }

        function renderTable(selector, events, type) {
            const tableBody = document.querySelector(selector);
            tableBody.innerHTML = '';

            if (events.length === 0) {
                tableBody.innerHTML = `<tr><td colspan="5" class="info-kosong">Tidak ada agenda.</td></tr>`;
                return;
            }

            events.forEach(event => {
                const row = document.createElement('tr');
                let attendanceHtml = `
                    <td>
                        ${event.attendances_count} / ${event.invited_count} (${event.attendance_percentage}%)
                        <div class="progress-bar-container">
                            <div class="progress-bar" style="width: ${event.attendance_percentage}%;"></div>
                        </div>
                    </td>
                `;

                let rowContent = '';
                if (type === 'akan_datang') {
                    rowContent = `
                        <td>${event.title}</td>
                        <td>${formatDate(event.start_time)}</td>
                        <td>${event.location}</td>
                        <td>${event.invited_count} orang</td>
                        <td class="actions">
                            <a href="/admin/events/${event.id}/show-qrcode" target="_blank" class="btn-qr">Tampilkan QR</a>
                        </td>
                    `;
                } else { // Berlangsung & Selesai
                     rowContent = `
                        <td>${event.title}</td>
                        <td>${type === 'berlangsung' ? 'Berakhir ' + formatDate(event.end_time) : formatDate(event.start_time)}</td>
                        <td>${event.location}</td>
                        ${attendanceHtml}
                        <td class="actions">
                             <a href="/admin/events/${event.id}/show-qrcode" target="_blank" class="btn-qr">Tampilkan QR</a>
                             <a href="#" onclick="showAttendees(${event.id}, '${event.title}')" class="btn-attendees">Lihat Peserta</a>
                        </td>
                    `;
                }
                row.innerHTML = rowContent;
                tableBody.appendChild(row);
            });
        }

        async function loadAdminIndex() {
            const loadingIndicator = document.getElementById('loading-indicator');
            try {
                const response = await fetch(API_URL, {
                    headers: {
                        'Authorization': `Bearer ${ADMIN_AUTH_TOKEN}`,
                        'Accept': 'application/json'
                    }
                });
                if (!response.ok) throw new Error('Gagal memuat data rekapitulasi');

                const result = await response.json();
                if (result.success) {
                    renderTable('#table-akan-datang tbody', result.data.akan_datang, 'akan_datang');
                    renderTable('#table-berlangsung tbody', result.data.berlangsung, 'berlangsung');
                    renderTable('#table-selesai tbody', result.data.selesai, 'selesai');
                }
            } catch (error) {
                console.error("Error:", error);
                document.querySelector('#akan-datang-container').innerHTML = '<p class="info-kosong">Gagal memuat data.</p>';
            } finally {
                loadingIndicator.style.display = 'none';
            }
        }
        
        // --- FUNGSI BARU UNTUK MENAMPILKAN DAFTAR PESERTA ---
        const modal = document.getElementById('attendees-modal');
        const modalTitle = document.getElementById('modal-title');
        const attendeesTableBody = document.querySelector('#attendees-table tbody');

        async function showAttendees(eventId, eventTitle) {
            modal.style.display = 'flex';
            modalTitle.innerText = `Daftar Hadir: ${eventTitle}`;
            attendeesTableBody.innerHTML = '<tr><td colspan="3" class="info-kosong">Memuat data peserta...</td></tr>';

            try {
                const response = await fetch(`/api/events/${eventId}/attendees`, {
                     headers: {
                        'Authorization': `Bearer ${ADMIN_AUTH_TOKEN}`,
                        'Accept': 'application/json'
                    }
                });
                if (!response.ok) throw new Error('Gagal mengambil data peserta.');

                const result = await response.json();
                attendeesTableBody.innerHTML = ''; // Kosongkan lagi

                if (result.success && result.data.length > 0) {
                    result.data.forEach(attendee => {
                        const row = `
                            <tr>
                                <td>${attendee.nik}</td>
                                <td>${attendee.nama}</td>
                                <td>${attendee.phone || '-'}</td>
                            </tr>
                        `;
                        attendeesTableBody.innerHTML += row;
                    });
                } else {
                    attendeesTableBody.innerHTML = '<tr><td colspan="3" class="info-kosong">Belum ada peserta yang hadir.</td></tr>';
                }
            } catch (error) {
                console.error("Error fetching attendees:", error);
                attendeesTableBody.innerHTML = '<tr><td colspan="3" class="info-kosong" style="color: red;">Gagal memuat data.</td></tr>';
            }
        }

        function closeModal() {
            modal.style.display = 'none';
        }

        // Tutup modal jika klik di luar area konten
        window.onclick = function(event) {
            if (event.target == modal) {
                closeModal();
            }
        }

        document.addEventListener('DOMContentLoaded', loadAdminIndex);
    </script>
</body>
</html>
