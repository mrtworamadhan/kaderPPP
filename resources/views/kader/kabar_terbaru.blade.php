<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kabar Terbaru</title>
    <style>
        body, html { margin: 0; padding: 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #f0f2f5; color: #333; }
        .container { padding: 15px; }
        .header { padding: 10px 15px; background-color: #00573d; color: white; font-size: 20px; font-weight: 600; }
        .kabar-list { margin-top: 15px; }
        .kabar-card { background: #fff; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); margin-bottom: 15px; overflow: hidden; }
        .kabar-card img { width: 100%; height: 180px; object-fit: cover; }
        .kabar-content { padding: 15px; }
        .kabar-content h3 { margin: 0 0 10px; font-size: 16px; color: #004d35; }
        .kabar-content p { font-size: 14px; color: #555; margin: 0 0 15px; }
        .kabar-footer { display: flex; justify-content: space-between; align-items: center; }
        .kabar-footer small { font-size: 12px; color: #888; }
        .share-btn { background-color: #28a745; color: white; border: none; padding: 8px 12px; border-radius: 6px; font-size: 12px; cursor: pointer; }
        #load-more-container { text-align: center; padding: 20px; }
        #load-more-btn { background-color: #00573d; color: white; border: none; padding: 12px 25px; border-radius: 8px; font-size: 14px; font-weight: 500; cursor: pointer; }
        #loading-indicator { text-align: center; padding: 40px; font-size: 16px; color: #606770; }
    </style>
</head>
<body>
    <div class="header">Kabar Terbaru</div>
    <div class="container">
        <div id="kabar-list"></div>
        <div id="loading-indicator">Memuat berita...</div>
        <div id="load-more-container" style="display: none;">
            <button id="load-more-btn">Muat Lebih Banyak</button>
        </div>
    </div>

    <script>
        const API_URL = '/api/kabar-terbaru';
        const KABAR_LIST = document.getElementById('kabar-list');
        const LOADING_INDICATOR = document.getElementById('loading-indicator');
        const LOAD_MORE_CONTAINER = document.getElementById('load-more-container');
        const LOAD_MORE_BTN = document.getElementById('load-more-btn');

        let nextPageUrl = null;
        let authToken = null;

        function renderKabar(kabarList) {
            kabarList.forEach(kabar => {
                const defaultImage = 'https://via.placeholder.com/400x180';
                const shareButton = kabar.share_link ? `<button class="share-btn" onclick="shareContent('${kabar.share_link}')">Share & Dapatkan Poin</button>` : '';
                const card = document.createElement('div');
                card.className = 'kabar-card';
                card.innerHTML = `
                    <img src="${kabar.foto || defaultImage}" alt="Foto Kabar">
                    <div class="kabar-content">
                        <h3>${kabar.judul}</h3>
                        <p>${kabar.deskripsi || ''}</p>
                        <div class="kabar-footer">
                            <small>${new Date(kabar.created_at).toLocaleDateString('id-ID', {day:'numeric', month:'long', year:'numeric'})}</small>
                            ${shareButton}
                        </div>
                    </div>
                `;
                KABAR_LIST.appendChild(card);
            });
        }

        async function loadKabar(url) {
            LOADING_INDICATOR.style.display = 'block';
            LOAD_MORE_CONTAINER.style.display = 'none';
            try {
                const response = await fetch(url, {
                    headers: { 'Authorization': `Bearer ${authToken}`, 'Accept': 'application/json' }
                });
                if (!response.ok) throw new Error('Gagal memuat berita');
                
                const result = await response.json();
                if (result.success) {
                    const pagination = result.data;
                    renderKabar(pagination.data);
                    nextPageUrl = pagination.next_page_url;
                    if (nextPageUrl) {
                        LOAD_MORE_CONTAINER.style.display = 'block';
                    }
                }
            } catch (error) {
                console.error("Error:", error);
                KABAR_LIST.innerHTML = '<p>Gagal memuat berita.</p>';
            } finally {
                LOADING_INDICATOR.style.display = 'none';
            }
        }

        function shareContent(link) {
            if (window.Kodular) {
                window.Kodular.Share.Message(link);
            } else {
                navigator.clipboard.writeText(link).then(() => alert('Link share disalin!'));
            }
        }

        LOAD_MORE_BTN.addEventListener('click', () => {
            if (nextPageUrl) {
                loadKabar(nextPageUrl);
            }
        });

        function start(token) {
            authToken = token;
            loadKabar(API_URL); // Memuat halaman pertama
        }

        // Untuk testing di browser
        document.addEventListener('DOMContentLoaded', () => {
             const TEST_TOKEN = '6|yuAL1icCCjvNkoOZhZ4L2kit07ixxHK3sUO8g1Ccc00ef44c';
             start(TEST_TOKEN);
        });
    </script>
</body>
</html>
