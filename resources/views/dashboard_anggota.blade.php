<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Statistik Anggota</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            margin: 0;
            padding: 15px;
            background-color: #ffffffff;
        }

        .container {
            max-width: 900px;
            margin: auto;
        }

        h2,
        h3 {
            text-align: center;
            color: #1c1e21;
        }

        /* --- PERBAIKAN CSS LAYOUT & KARTU --- */
        .stat-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 15px;
            margin-bottom: 30px;
        }

        .stat-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        .stat-card {
            background: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            padding: 20px;
            text-align: center;
        }

        .stat-card .label {
            font-size: 14px;
            color: #606770;
            margin-bottom: 8px;
        }

        .stat-card .value {
            font-size: 28px;
            font-weight: 700;
            color: #1c1e21;
        }

        .stat-card .percentage {
            font-size: 14px;
            font-weight: 500;
            margin-top: 5px;
            color: #606770;
        }

        /* --- PERBAIKAN CSS WARNA LATAR BELAKANG --- */
        
        /* Mengubah semua teks di dalam kartu menjadi putih jika ada kelas warna */
        .color-blue, .color-green, .color-orange, .color-skyblue, .color-pink {
            color: #ffffff;
        }
        .color-blue .label, .color-blue .value, .color-blue .percentage,
        .color-green .label, .color-green .value, .color-green .percentage,
        .color-orange .label, .color-orange .value, .color-orange .percentage,
        .color-skyblue .label, .color-skyblue .value, .color-skyblue .percentage,
        .color-pink .label, .color-pink .value, .color-pink .percentage {
            color: #ffffff; /* Paksa semua text jadi putih */
        }

        /* Mengganti warna background-nya */
        .color-blue { background-color: #63d263ff; }
        .color-green { background-color: #116811ff; }
        .color-orange { background-color: #fd7e14; }
        .color-skyblue { background-color: #0dcaf0; }
        .color-pink { background-color: #d63384; }

        .search-container {
            background: #fff;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .search-container input {
            width: 100%;
            padding: 12px;
            font-size: 16px;
            border: 1px solid #ccd0d5;
            border-radius: 6px;
            box-sizing: border-box;
        }

        .table-wrapper {
            overflow-x: auto;
        }

        .results-container table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            white-space: nowrap;
        }

        .results-container th,
        .results-container td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #e9ebee;
        }

        .results-container th {
            background-color: #116811ff;
            text-align: center;
            font-size: 14px;
            color: #fdb71fff;
        }

        #search-info {
            text-align: center;
            padding: 20px;
            color: #606770;
        }

        .pagination-container {
            text-align: center;
            margin-top: 20px;
        }

        .pagination-container button {
            padding: 8px 16px;
            margin: 0 5px;
            border: 1px solid #ccd0d5;
            background-color: #fff;
            cursor: pointer;
            border-radius: 6px;
        }

        .pagination-container button:disabled {
            cursor: not-allowed;
            opacity: 0.5;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>Statistik Anggota</h2>
        <div class="stat-grid">
            <div class="stat-card color-green">
                <div class="label">Total Anggota</div>
                <div class="value" id="total-anggota">-</div>
            </div>
            <div class="stat-row">
                <div class="stat-card color-blue">
                    <div class="label">Kader Muda (< 35 Thn)</div>
                            <div class="value" id="total-muda">-</div>
                            <div class="percentage" id="persen-muda"></div>
                    </div>
                    <div class="stat-card color-orange">
                        <div class="label">Kader Senior (>= 35 Thn)</div>
                        <div class="value" id="total-senior">-</div>
                        <div class="percentage" id="persen-senior"></div>
                    </div>
                </div>
                <div class="stat-row">
                    <div class="stat-card color-skyblue">
                        <div class="label">Laki-laki</div>
                        <div class="value" id="total-laki">-</div>
                    </div>
                    <div class="stat-card color-pink">
                        <div class="label">Perempuan</div>
                        <div class="value" id="total-perempuan">-</div>
                    </div>
                </div>
            </div>
            <h3>Pencarian Anggota</h3>
            <div class="search-container">
                <input type="text" id="searchInput" placeholder="Ketik NIK atau Nama Anggota...">
            </div>

            <div class="table-wrapper">
                <div class="results-container">
                    <table id="resultsTable">
                        <thead>
                            <tr>
                                <th>NIK</th>
                                <th>Nama</th>
                                <th>No. HP</th>
                                <th>Jabatan</th>
                                <th>Wilayah</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                    <p id="search-info">Memuat data anggota awal...</p>
                </div>
            </div>
            <div class="pagination-container" id="pagination-links" style="display: none;"></div>
        </div>

        <script>
            // ... (seluruh kode JavaScript dari langkah sebelumnya bisa langsung di-paste di sini) ...
            const BASE_API_URL = '/api/dashboard/anggota';
            const searchInput = document.getElementById('searchInput');
            const resultsTableBody = document.querySelector("#resultsTable tbody");
            const searchInfo = document.getElementById('search-info');
            const paginationContainer = document.getElementById('pagination-links');

            async function loadStatistik() {
                try {
                    const response = await fetch(`${BASE_API_URL}/statistik`);
                    const result = await response.json();
                    if (result.success) {
                        const data = result.data;
                        document.getElementById('total-anggota').innerText = data.total_anggota.toLocaleString('id-ID');
                        document.getElementById('total-muda').innerText = data.kelompok_usia.muda.toLocaleString('id-ID');
                        document.getElementById('persen-muda').innerText = `${data.kelompok_usia.persentase_muda}%`;
                        document.getElementById('total-senior').innerText = data.kelompok_usia.senior.toLocaleString('id-ID');
                        document.getElementById('persen-senior').innerText = `${data.kelompok_usia.persentase_senior}%`;
                        document.getElementById('total-laki').innerText = data.gender.laki_laki.toLocaleString('id-ID');
                        document.getElementById('total-perempuan').innerText = data.gender.perempuan.toLocaleString('id-ID');
                    }
                } catch (error) { console.error("Gagal memuat statistik:", error); }
            }

            async function performSearch(url) {
                searchInfo.innerText = 'Memuat data...';
                paginationContainer.style.display = 'none';
                try {
                    const response = await fetch(url);
                    const result = await response.json();

                    resultsTableBody.innerHTML = '';
                    if (result.success) {
                        const pagination = result.data;
                        if (pagination.total > 0) {
                            searchInfo.innerText = `Menampilkan ${pagination.from}-${pagination.to} dari ${pagination.total} hasil.`;
                            pagination.data.forEach(anggota => {
                                const row = `
                                <tr>
                                    <td>${anggota.nik}</td>
                                    <td>${anggota.nama}</td>
                                    <td>${anggota.phone || '-'}</td>
                                    <td>${anggota.jabatan || '-'}</td>
                                    <td>${anggota.wilayah_lengkap || '-'}</td>
                                </tr>
                            `;
                                resultsTableBody.innerHTML += row;
                            });
                            renderPagination(pagination.links);
                        } else {
                            searchInfo.innerText = 'Tidak ada anggota yang ditemukan.';
                        }
                    } else {
                        searchInfo.innerText = result.message || 'Anggota tidak ditemukan.';
                    }
                } catch (error) {
                    console.error("Gagal melakukan pencarian:", error);
                    searchInfo.innerText = 'Terjadi kesalahan saat mencari.';
                }
            }

            let searchTimeout;
            searchInput.addEventListener('input', () => {
                clearTimeout(searchTimeout);
                const keyword = searchInput.value;

                if (keyword.length === 0) {
                    performSearch(`${BASE_API_URL}/cari`);
                    return;
                }
                if (keyword.length < 3) {
                    resultsTableBody.innerHTML = '';
                    searchInfo.innerText = 'Ketik minimal 3 karakter untuk memulai pencarian.';
                    paginationContainer.style.display = 'none';
                    return;
                }
                searchTimeout = setTimeout(() => {
                    performSearch(`${BASE_API_URL}/cari?keyword=${keyword}`);
                }, 500);
            });

            function renderPagination(links) {
                paginationContainer.innerHTML = '';
                if (links.length > 3) {
                    paginationContainer.style.display = 'block';
                    links.forEach(link => {
                        const button = document.createElement('button');
                        button.innerHTML = link.label.replace('&laquo;', '«').replace('&raquo;', '»');
                        button.disabled = !link.url || link.active;
                        if (link.active) {
                            button.style.backgroundColor = '#1877f2';
                            button.style.color = '#fff';
                        }
                        if (link.url) { button.onclick = () => performSearch(link.url); }
                        paginationContainer.appendChild(button);
                    });
                }
            }

            function initializeDashboard() {
                loadStatistik();
                performSearch(`${BASE_API_URL}/cari`);
            }

            document.addEventListener('DOMContentLoaded', initializeDashboard);
        </script>
</body>

</html>