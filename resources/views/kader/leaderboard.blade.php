<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Peringkat Kader</title>
    <style>
        body, html { margin: 0; padding: 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #f0f2f5; color: #333; }
        .container { padding: 15px; max-width: 800px; margin: auto; }
        .header { padding: 10px 15px; background-color: #00573d; color: white; font-size: 20px; font-weight: 600; text-align: center; }
        .filter-bar { display: flex; justify-content: flex-end; align-items: center; margin: 15px 0; }
        .filter-bar label { font-size: 14px; margin-right: 10px; color: #555; }
        .filter-bar select { padding: 8px; border-radius: 6px; border: 1px solid #ccc; }

        /* --- CSS BARU UNTUK PODIUM LEADERBOARD --- */
        .leaderboard-top3 {
            display: flex; /* Menggunakan flexbox untuk kontrol alignment */
            justify-content: center;
            align-items: flex-end; /* Kunci: Sejajarkan item di bagian bawah */
            gap: 5%; /* Jarak antar kartu */
            margin-bottom: 30px;
            padding: 20px 0;
            min-height: 280px; /* Beri tinggi minimal untuk kontainer */
        }
        .leaderboard-card {
            background: #fff;
            border-radius: 12px;
            text-align: center;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            border: 1px solid #e9ebee;
            width: 30%; /* Lebar setiap kartu */
            box-sizing: border-box;
            padding: 15px 10px;
        }
        
        /* Pengaturan Podium: 1 (kiri), 2 (tengah), 3 (kanan) dengan tinggi berbeda */
        .leaderboard-card.rank-1 { 
            order: 1; 
            height: 250px; 
            background: linear-gradient(145deg, #FFD700, #FFA500); 
            color: #4c3a00;
        }
        .leaderboard-card.rank-2 { 
            order: 2; 
            height: 220px;
            background: linear-gradient(145deg, #E0E0E0, #BDBDBD); 
            color: #333;
        }
        .leaderboard-card.rank-3 { 
            order: 3; 
            height: 190px;
            background: linear-gradient(145deg, #D2B48C, #A0522D); 
            color: #fff;
        }
        
        /* Ukuran elemen di dalam kartu dibuat responsif terhadap tinggi kartu */
        .leaderboard-card .photo {
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid rgba(255,255,255,0.5);
            margin: 0 auto 10px;
        }
        .rank-1 .photo { width: 80px; height: 80px; }
        .rank-2 .photo { width: 70px; height: 70px; }
        .rank-3 .photo { width: 60px; height: 60px; }

        .leaderboard-card .name { font-weight: 600; margin-bottom: 5px; }
        .rank-1 .name { font-size: 18px; }
        .rank-2 .name { font-size: 16px; }
        .rank-3 .name { font-size: 14px; }

        .leaderboard-card .points { font-weight: 700; }
        .rank-1 .points { font-size: 20px; }
        .rank-2 .points { font-size: 18px; }
        .rank-3 .points { font-size: 16px; }
        
        .leaderboard-card .rank { font-size: 14px; font-weight: 500; opacity: 0.8; margin-top: 5px;}
        
        /* Styling untuk peringkat 4 ke bawah (tetap sama) */
        .leaderboard-others .item { display: flex; align-items: center; background: #fff; border-radius: 8px; padding: 10px 15px; margin-bottom: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
        .leaderboard-others .rank { font-size: 16px; font-weight: 600; color: #888; width: 40px; text-align: center; }
        .leaderboard-others .photo { width: 40px; height: 40px; border-radius: 50%; object-fit: cover; margin: 0 15px; background-color: #eee;}
        .leaderboard-others .info { flex-grow: 1; }
        .leaderboard-others .info .name { font-weight: 500; font-size: 15px; }
        .leaderboard-others .info .points { font-size: 13px; color: #00573d; }

        #loading-indicator, #empty-state { text-align: center; padding: 40px; font-size: 16px; color: #606770; }
        .pagination-container { text-align: center; margin-top: 20px; }
        .pagination-container button { padding: 8px 16px; margin: 0 5px; border: 1px solid #ccd0d5; background-color: #fff; cursor: pointer; border-radius: 6px; }
        .pagination-container button:disabled { cursor: not-allowed; opacity: 0.5; }
    </style>
</head>
<body>
    <div class="header">Peringkat Kader Teraktif</div>
    <div class="container">

        <div id="leaderboard-top3" class="leaderboard-top3"></div>
        <div id="leaderboard-others" class="leaderboard-others"></div>
        
        <div id="loading-indicator">Memuat peringkat...</div>
        <div id="empty-state" style="display: none;">Tidak ada data peringkat.</div>
        <div class="filter-bar">
            <label for="limitFilter">Tampilkan:</label>
            <select id="limitFilter">
                <option value="10" selected>10</option>
                <option value="15">15</option>
                <option value="20">20</option>
                <option value="25">25</option>
            </select>
        </div>
        <div class="pagination-container" id="pagination-links" style="display: none;"></div>
    </div>

    <script>
        const API_URL = '/api/leaderboard';
        const TOP3_CONTAINER = document.getElementById('leaderboard-top3');
        const OTHERS_CONTAINER = document.getElementById('leaderboard-others');
        const LOADING_INDICATOR = document.getElementById('loading-indicator');
        const EMPTY_STATE = document.getElementById('empty-state');
        const PAGINATION_CONTAINER = document.getElementById('pagination-links');
        const LIMIT_FILTER = document.getElementById('limitFilter');

        let authToken = null;

        function renderLeaderboard(pagination) {
            TOP3_CONTAINER.innerHTML = '';
            OTHERS_CONTAINER.innerHTML = '';

            // --- PERBAIKAN DI SINI ---
            // Sembunyikan atau tampilkan kontainer podium berdasarkan halaman
            if (pagination.current_page === 1 && pagination.data.length > 0) {
                TOP3_CONTAINER.style.display = 'flex';
            } else {
                TOP3_CONTAINER.style.display = 'none';
            }
            // --- BATAS PERBAIKAN ---

            if (pagination.data.length === 0) {
                EMPTY_STATE.style.display = 'block';
                return;
            }
            EMPTY_STATE.style.display = 'none';

            pagination.data.forEach((anggota, index) => {
                const rank = pagination.from + index;
                const defaultPhoto = 'https://via.placeholder.com/70';

                // Hanya tampilkan podium di halaman pertama
                if (rank <= 3 && pagination.current_page === 1) {
                    const card = document.createElement('div');
                    card.className = `leaderboard-card rank-${rank}`;
                    card.innerHTML = `
                        <img src="${anggota.foto || defaultPhoto}" alt="${anggota.nama}" class="photo">
                        <div class="name">${anggota.nama}</div>
                        <div class="points">${anggota.total_poin.toLocaleString('id-ID')} Poin</div>
                        <div class="rank">Peringkat ${rank}</div>
                    `;
                    TOP3_CONTAINER.appendChild(card);
                } else {
                    // Render Peringkat 4 ke bawah
                    const item = document.createElement('div');
                    item.className = 'item';
                    item.innerHTML = `
                        <div class="rank">${rank}</div>
                        <img src="${anggota.foto || defaultPhoto.replace('70','40')}" alt="${anggota.nama}" class="photo">
                        <div class="info">
                            <div class="name">${anggota.nama}</div>
                            <div class="points">${anggota.total_poin.toLocaleString('id-ID')} Poin</div>
                        </div>
                    `;
                    OTHERS_CONTAINER.appendChild(item);
                }
            });
        }

        async function loadLeaderboard(url) {
            LOADING_INDICATOR.style.display = 'block';
            // TOP3_CONTAINER.innerHTML = ''; // Kita pindahkan ini ke renderLeaderboard
            // OTHERS_CONTAINER.innerHTML = '';
            PAGINATION_CONTAINER.style.display = 'none';
            EMPTY_STATE.style.display = 'none';

            try {
                const response = await fetch(url, {
                    headers: { 'Authorization': `Bearer ${authToken}`, 'Accept': 'application/json' }
                });
                if (!response.ok) throw new Error('Gagal memuat leaderboard');
                
                const result = await response.json();
                if (result.success) {
                    renderLeaderboard(result.data);
                    renderPagination(result.data.links);
                }
            } catch (error) {
                console.error("Error:", error);
                EMPTY_STATE.innerText = 'Gagal memuat data.';
                EMPTY_STATE.style.display = 'block';
            } finally {
                LOADING_INDICATOR.style.display = 'none';
            }
        }

        function renderPagination(links) {
            PAGINATION_CONTAINER.innerHTML = '';
            if (links.length > 3) {
                PAGINATION_CONTAINER.style.display = 'block';
                links.forEach(link => {
                    const button = document.createElement('button');
                    button.innerHTML = link.label.replace('&laquo;', '«').replace('&raquo;', '»');
                    button.disabled = !link.url || link.active;
                    if(link.active) { button.style.backgroundColor = '#00573d'; button.style.color = '#fff'; }
                    if (link.url) {
                        button.onclick = () => loadLeaderboard(`${link.url}&limit=${LIMIT_FILTER.value}`);
                    }
                    PAGINATION_CONTAINER.appendChild(button);
                });
            }
        }

        LIMIT_FILTER.addEventListener('change', () => {
             loadLeaderboard(`${API_URL}?limit=${LIMIT_FILTER.value}`);
        });

        function start(token) {
            authToken = token;
            loadLeaderboard(`${API_URL}?limit=${LIMIT_FILTER.value}`);
        }

        document.addEventListener('DOMContentLoaded', () => {
             const TEST_TOKEN = '6|yuAL1icCCjvNkoOZhZ4L2kit07ixxHK3sUO8g1Ccc00ef44c';
             start(TEST_TOKEN);
        });
    </script>
</body>
</html>
