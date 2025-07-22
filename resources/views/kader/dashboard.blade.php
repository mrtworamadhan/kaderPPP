<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Kader</title>
    <style>
        body,
        html {
            margin: 0;
            padding: 0;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: #f0f2f5;
            color: #333;
        }

        .container {
            padding: 15px;
        }

        h2 {
            font-size: 20px;
            color: #00573d;
            margin: 25px 0 15px 0;
            border-left: 4px solid #f9a825;
            padding-left: 10px;
        }

        /* Section A: KTA */
        .kta-card {
            background: linear-gradient(135deg, #00573d, #28a745);
            color: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 15px rgba(0, 87, 61, 0.3);
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .kta-photo {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            border: 2px solid white;
            object-fit: cover;
            background-color: #eee;
        }

        .kta-info {
            flex-grow: 1;
        }

        .kta-info h3 {
            margin: 0;
            font-size: 18px;
            font-weight: 600;
        }

        .kta-info p {
            margin: 5px 0 0;
            font-size: 14px;
            opacity: 0.9;
        }

        /* Section B: Poin & Rewards */
        .poin-card {
            background: #fff;
            border-radius: 8px;
            padding: 15px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .poin-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }

        .poin-header .label {
            font-size: 14px;
            color: #606770;
        }

        .poin-header .value {
            font-size: 24px;
            font-weight: 700;
            color: #00573d;
        }

        .reward-progress p {
            margin: 0 0 5px;
            font-size: 12px;
            color: #333;
            text-align: center;
        }

        .progress-bar {
            background-color: #e9ebee;
            border-radius: 5px;
            height: 12px;
            overflow: hidden;
        }

        .progress {
            background-color: #f9a825;
            height: 100%;
            border-radius: 5px;
            transition: width 0.5s ease-in-out;
            text-align: center;
            color: white;
            font-size: 10px;
            line-height: 12px;
        }

        /* Section C, D, E: Info Cards */
        .info-card {
            background: #fff;
            border-radius: 8px;
            padding: 15px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            margin-bottom: 15px;
        }

        .info-card h3 {
            margin: 0 0 10px;
            font-size: 16px;
            color: #004d35;
        }

        .info-card p {
            font-size: 14px;
            color: #555;
            margin: 5px 0;
        }

        .info-card-empty {
            text-align: center;
            color: #888;
            padding: 20px;
        }

        /* Kabar Terbaru List */
        .kabar-item {
            display: flex;
            gap: 15px;
            align-items: center;
            border-bottom: 1px solid #eee;
            padding: 10px 0;
        }

        .kabar-item:last-child {
            border-bottom: none;
        }

        .kabar-item img {
            width: 50px;
            height: 50px;
            border-radius: 8px;
            object-fit: cover;
        }

        .kabar-item-info {
            flex-grow: 1;
        }

        .kabar-item-info p {
            margin: 0;
            font-size: 14px;
            font-weight: 500;
        }

        .kabar-item-info small {
            font-size: 12px;
            color: #888;
        }

        .share-btn {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 8px 12px;
            border-radius: 6px;
            font-size: 12px;
            cursor: pointer;
        }

        #loading-indicator {
            text-align: center;
            padding: 40px;
            font-size: 16px;
            color: #606770;
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Section A: KTA -->
        <div id="kta-section"></div>

        <!-- Section B: Poin & Rewards -->
        <h2>Poin & Hadiah</h2>
        <div id="poin-section"></div>

        <!-- Section C: Arahan Ketua -->
        <h2>Instruksi dan Arahan</h2>
        <div id="arahan-section"></div>

        <!-- Section D: Agenda Terdekat -->
        <h2>Agenda Terdekat</h2>
        <div id="agenda-section"></div>

        <!-- Section E: Kabar Terbaru -->
        <h2>Kabar Terbaru</h2>
        <div id="kabar-section" class="info-card"></div>

        <div id="loading-indicator">Memuat dashboard...</div>
    </div>

    <script>
        const API_URL = '/api/kader/dashboard';
        const KTA_SECTION = document.getElementById('kta-section');
        const POIN_SECTION = document.getElementById('poin-section');
        const ARAHAN_SECTION = document.getElementById('arahan-section');
        const AGENDA_SECTION = document.getElementById('agenda-section');
        const KABAR_SECTION = document.getElementById('kabar-section');
        const LOADING_INDICATOR = document.getElementById('loading-indicator');

        function renderKTA(ktaData) {
            const defaultPhoto = 'https://via.placeholder.com/60';
            const logoPartai = '/images/logo.png'; // sesuaikan dengan lokasi file kamu

            KTA_SECTION.innerHTML = `
            <div class="kta-card" style="display: flex; align-items: center; border: 1px solid #ccc; padding: 15px; border-radius: 10px;">
                <!-- Kiri: Foto dan info anggota -->
                <div style="display: flex; align-items: center; flex: 1;">
                <!-- Foto Profil -->
                <img 
                    src="${ktaData.foto ? '/' + ktaData.foto : defaultPhoto}" 
                    alt="Foto Profil" 
                    style="
                        width: 60px; 
                        height: 60px; 
                        border-radius: 50%; 
                        object-fit: cover; 
                        object-position: center;
                        aspect-ratio: 1 / 1;
                        background-color: #eee;
                    "
                >

                <!-- Info Nama & No. KTA -->
                <div style="margin-left: 12px;">
                    <h3 style="margin: 0; font-size: 18px;">${ktaData.nama}</h3>
                    <p style="margin: 4px 0 0 0; font-size: 12px;">No. KTA: ${ktaData.id_anggota || '-'}</p>
                </div>
            </div>

                <!-- Divider -->
                <div style="width: 1px; height: 60px; background-color: #ccc; margin: 0 10px;"></div>

                <!-- Kanan: Logo partai -->
                <div>
                    <img src="${logoPartai}" alt="Logo Partai" style="height: 60px;">
                </div>
            </div>
        `;
        }

        function renderPoin(userPoin, rewards) {
            let nextReward = null;
            for (const reward of rewards) {
                if (userPoin < reward.points_needed) {
                    nextReward = reward;
                    break;
                }
            }

            let progressHtml = '<p>Anda telah mencapai semua hadiah!</p>';
            if (nextReward) {
                const percentage = Math.round((userPoin / nextReward.points_needed) * 100);
                progressHtml = `
                    <p><strong>${(nextReward.points_needed - userPoin).toLocaleString('id-ID')} poin</strong> Lagi untuk mendapatkan <strong>${nextReward.name}</strong>!</p>
                    <div class="progress-bar">
                        <div class="progress" style="width: ${percentage}%;">${percentage}%</div>
                    </div>
                `;
            }

            POIN_SECTION.innerHTML = `
                <div class="poin-card">
                    <div class="poin-header">
                        <span class="label">Total Poin Anda</span>
                        <span class="value">${userPoin.toLocaleString('id-ID')}</span>
                    </div>
                    <div class="reward-progress">${progressHtml}</div>
                </div>
            `;
        }

        function renderArahan(arahan) {
            if (arahan) {
                ARAHAN_SECTION.innerHTML = `
                    <div class="info-card">
                        <h3>${arahan.judul}</h3>
                        <p>${arahan.arahan}</p>
                    </div>
                `;
            } else {
                ARAHAN_SECTION.innerHTML = '<p class="info-card-empty">Tidak ada arahan baru.</p>';
            }
        }

        function renderAgenda(agenda) {
            if (agenda) {
                const startTime = new Date(agenda.start_time).toLocaleString('id-ID', { dateStyle: 'full', timeStyle: 'short' });
                AGENDA_SECTION.innerHTML = `
                    <div class="info-card">
                        <h3>${agenda.title}</h3>
                        <p><strong>Waktu:</strong> ${startTime}</p>
                        <p><strong>Lokasi:</strong> ${agenda.location}</p>
                    </div>
                `;
            } else {
                AGENDA_SECTION.innerHTML = '<p class="info-card-empty">Tidak ada agenda terdekat.</p>';
            }
        }

        function renderKabar(kabarList) {
            if (kabarList && kabarList.length > 0) {
                KABAR_SECTION.innerHTML = ''; // Kosongkan dulu
                kabarList.forEach(kabar => {
                    const defaultImage = 'https://via.placeholder.com/50';
                    const shareButton = kabar.share_link ? `<button class="share-btn" onclick="shareContent('${kabar.share_link}')">Share</button>` : '';
                    KABAR_SECTION.innerHTML += `
                        <div class="kabar-item">
                            <img src="${kabar.foto || defaultImage}" alt="Foto Kabar">
                            <div class="kabar-item-info">
                                <p>${kabar.judul}</p>
                                <small>${new Date(kabar.created_at).toLocaleDateString('id-ID')}</small>
                                <small>${kabar.points_per_click} Poin</small>
                            </div>
                            ${shareButton}
                        </div>
                    `;
                });
            } else {
                KABAR_SECTION.innerHTML = '<p class="info-card-empty">Tidak ada kabar terbaru.</p>';
            }
        }

        function shareContent(link) {
            // Fungsi ini akan memicu dialog share di Kodular
            if (window.Kodular) {
                window.Kodular.Share.Message(link);
            } else {
                // Fallback untuk testing di browser
                navigator.clipboard.writeText(link).then(() => {
                    alert('Link share disalin ke clipboard!');
                });
            }
        }

        async function loadDashboard(authToken) {
            if (!authToken) {
                LOADING_INDICATOR.innerText = 'Error: Token otentikasi tidak ditemukan.';
                return;
            }
            try {
                const response = await fetch(API_URL, {
                    headers: {
                        'Authorization': `Bearer ${authToken}`,
                        'Accept': 'application/json'
                    }
                });
                if (!response.ok) throw new Error('Gagal mengambil data dashboard');

                const result = await response.json();
                if (result.success) {
                    const data = result.data;
                    renderKTA(data.kta);
                    renderPoin(data.kta.total_poin, data.rewards);
                    renderArahan(data.arahan_ketua);
                    renderAgenda(data.agenda_terdekat);
                    renderKabar(data.kabar_terbaru);
                }
            } catch (error) {
                console.error("Error:", error);
                document.body.innerHTML = '<p style="text-align:center; padding-top: 50px;">Gagal memuat dashboard.</p>';
            } finally {
                LOADING_INDICATOR.style.display = 'none';
            }
        }

        // --- JEMBATAN DENGAN KODULAR ---
        // Kodular akan memanggil fungsi ini untuk memulai semuanya
        function start(token) {
            loadDashboard(token);
        }

        // Untuk testing di browser, panggil dengan token manual
        document.addEventListener('DOMContentLoaded', () => {
            // Ganti dengan token kader yang valid untuk testing
            const TEST_TOKEN = '6|yuAL1icCCjvNkoOZhZ4L2kit07ixxHK3sUO8g1Ccc00ef44c';
            start(TEST_TOKEN);
        });
    </script>
</body>

</html>