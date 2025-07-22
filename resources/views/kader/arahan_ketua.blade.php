<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Arahan Ketua</title>
    <style>
        body, html { margin: 0; padding: 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #f0f2f5; color: #333; }
        .container { padding: 15px; }
        .header { padding: 10px 15px; background-color: #00573d; color: white; font-size: 20px; font-weight: 600; }
        .arahan-list { margin-top: 15px; }
        .arahan-card { background: #fff; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); margin-bottom: 15px; padding: 15px; border-left: 5px solid #f9a825; }
        .arahan-card h3 { margin: 0 0 10px; font-size: 16px; color: #004d35; }
        .arahan-card p { font-size: 14px; color: #555; margin: 0 0 10px; line-height: 1.5; }
        .arahan-card small { font-size: 12px; color: #888; font-weight: 500; }
        #load-more-container { text-align: center; padding: 20px; }
        #load-more-btn { background-color: #00573d; color: white; border: none; padding: 12px 25px; border-radius: 8px; font-size: 14px; font-weight: 500; cursor: pointer; }
        #loading-indicator { text-align: center; padding: 40px; font-size: 16px; color: #606770; }
    </style>
</head>
<body>
    <div class="header">Arahan Ketua</div>
    <div class="container">
        <div id="arahan-list"></div>
        <div id="loading-indicator">Memuat arahan...</div>
        <div id="load-more-container" style="display: none;">
            <button id="load-more-btn">Muat Lebih Banyak</button>
        </div>
    </div>

    <script>
        const API_URL = '/api/arahan-ketua'; // Pastikan rute ini menunjuk ke arahanIndex()
        const ARAHAN_LIST = document.getElementById('arahan-list');
        const LOADING_INDICATOR = document.getElementById('loading-indicator');
        const LOAD_MORE_CONTAINER = document.getElementById('load-more-container');
        const LOAD_MORE_BTN = document.getElementById('load-more-btn');

        let nextPageUrl = null;
        let authToken = null;

        function renderArahan(arahanList) {
            // Cek jika list kosong saat pertama kali load
            if (arahanList.length === 0 && ARAHAN_LIST.innerHTML === '') {
                ARAHAN_LIST.innerHTML = '<p style="text-align:center;">Tidak ada arahan terbaru.</p>';
            }
            
            arahanList.forEach(arahan => {
                const card = document.createElement('div');
                card.className = 'arahan-card';
                card.innerHTML = `
                    <h3>${arahan.judul || 'Arahan Ketua'}</h3>
                    <p>${arahan.arahan}</p>
                    <small>${new Date(arahan.tanggal).toLocaleDateString('id-ID', {weekday: 'long', year: 'numeric', month: 'long', day: 'numeric'})}</small>
                `;
                ARAHAN_LIST.appendChild(card);
            });
        }

        async function loadArahan(url) {
            LOADING_INDICATOR.style.display = 'block';
            LOAD_MORE_CONTAINER.style.display = 'none';
            try {
                const response = await fetch(url, {
                    headers: { 'Authorization': `Bearer ${authToken}`, 'Accept': 'application/json' }
                });
                if (!response.ok) throw new Error('Gagal memuat arahan');
                
                const result = await response.json();
                
                // --- PERBAIKAN DI SINI ---
                // Cek dulu apakah result.data ada dan merupakan objek
                if (result.success && result.data && typeof result.data === 'object') {
                    const pagination = result.data;
                    renderArahan(pagination.data || []); // Beri nilai default array kosong
                    nextPageUrl = pagination.next_page_url;
                    
                    if (nextPageUrl) {
                        LOAD_MORE_CONTAINER.style.display = 'block';
                    }
                } else {
                    // Jika result.data null atau bukan objek, anggap tidak ada data
                    renderArahan([]);
                }
            } catch (error) {
                console.error("Error:", error);
                ARAHAN_LIST.innerHTML = '<p style="text-align:center; color:red;">Gagal memuat data.</p>';
            } finally {
                LOADING_INDICATOR.style.display = 'none';
            }
        }

        LOAD_MORE_BTN.addEventListener('click', () => {
            if (nextPageUrl) {
                loadArahan(nextPageUrl);
            }
        });

        function start(token) {
            authToken = token;
            loadArahan(API_URL); // Memuat halaman pertama
        }

        // Untuk testing di browser
        document.addEventListener('DOMContentLoaded', () => {
             const TEST_TOKEN = '6|yuAL1icCCjvNkoOZhZ4L2kit07ixxHK3sUO8g1Ccc00ef44c';
             start(TEST_TOKEN);
        });
    </script>
</body>
</html>
