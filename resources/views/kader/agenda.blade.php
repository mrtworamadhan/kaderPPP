<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agenda Kegiatan</title>
    <style>
        /* CSS Reset & Basic Styling */
        body,
        html {
            margin: 0;
            padding: 0;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: #f0f2f5;
            color: #333;
        }

        .container {
            padding: 20px;
        }

        h2 {
            font-size: 20px;
            color: #00573d;
            /* Hijau tua khas PPP */
            margin-bottom: 15px;
            border-left: 4px solid #f9a825;
            /* Aksen kuning */
            padding-left: 10px;
        }

        /* Styling untuk Kartu Agenda */
        .agenda-card {
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            margin-bottom: 15px;
            padding: 15px;
            border-left: 5px solid #28a745;
            /* Hijau cerah */
        }

        .agenda-card.berlangsung {
            border-left-color: #dc3545;
            /* Merah untuk yang sedang berlangsung */
        }

        .agenda-card h3 {
            font-size: 16px;
            margin-top: 0;
            margin-bottom: 10px;
            color: #004d35;
        }

        .agenda-card p {
            font-size: 14px;
            margin: 5px 0;
            color: #555;
            display: flex;
            align-items: center;
        }

        .agenda-card p svg {
            margin-right: 8px;
            fill: #666;
        }

        .info-kosong {
            text-align: center;
            color: #888;
            padding: 30px;
            background-color: #fff;
            border-radius: 8px;
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
        <!-- Bagian untuk Agenda Sedang Berlangsung -->
        <div id="berlangsung-container">
            <h2>Sedang Berlangsung</h2>
            <div id="list-berlangsung">
                <!-- Kartu agenda akan dimasukkan di sini oleh JavaScript -->
            </div>
        </div>

        <!-- Bagian untuk Agenda Akan Datang -->
        <div id="akan-datang-container" style="margin-top: 30px;">
            <h2>Akan Datang</h2>
            <div id="list-akan-datang">
                <!-- Kartu agenda akan dimasukkan di sini oleh JavaScript -->
            </div>
        </div>

        <!-- Indikator Loading -->
        <div id="loading-indicator">Memuat agenda...</div>
    </div>

    <script>
        const API_URL = '/api/events'; // URL API untuk mengambil daftar agenda
        const listBerlangsung = document.getElementById('list-berlangsung');
        const listAkanDatang = document.getElementById('list-akan-datang');
        const loadingIndicator = document.getElementById('loading-indicator');

        // Fungsi untuk membuat satu kartu agenda
        function createAgendaCard(event, isBerlangsung = false) {
            const card = document.createElement('div');
            card.className = 'agenda-card' + (isBerlangsung ? ' berlangsung' : '');

            // Format tanggal dan waktu ke format Indonesia
            const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' };
            const startTime = new Date(event.start_time).toLocaleString('id-ID', options);
            const endTime = new Date(event.end_time).toLocaleString('id-ID', options);

            card.innerHTML = `
                <h3>${event.title}</h3>
                <p>
                    <svg xmlns="http://www.w3.org/2000/svg" height="16" width="16" viewBox="0 0 576 512" fill="#facc15">
                        <path d="M287.9 17.8L354 150.2 499.2 171.5C522.2 174.6 531.5 202.4 514.6 217.8L406 313.2 
                            431.8 458.6C435.7 481.5 411.9 498.6 392 487.9L288 432.3 184 487.9C164.1 498.6 140.3 
                            481.5 144.2 458.6L170 313.2 61.4 217.8C44.5 202.4 53.8 174.6 76.8 171.5L222 150.2 
                            288.1 17.8C296.2 1.5 319.8 1.5 327.9 17.8z"/>
                    </svg>${event.points_reward} Poin
                </p>
                <p>
                    <svg xmlns="http://www.w3.org/2000/svg" height="16" width="12" viewBox="0 0 384 512"><path d="M215.7 499.2C267 435 384 279.4 384 192C384 86 298 0 192 0S0 86 0 192c0 87.4 117 243 168.3 307.2c12.3 15.3 35.1 15.3 47.4 0zM192 128a64 64 0 1 1 0 128 64 64 0 1 1 0-128z"/></svg>
                    ${event.location}
                </p>
                <p>
                    <svg xmlns="http://www.w3.org/2000/svg" height="16" width="14" viewBox="0 0 448 512"><path d="M152 24c0-13.3-10.7-24-24-24s-24 10.7-24 24V64H64V24c0-13.3-10.7-24-24-24S16 10.7 16 24V64H0V480H448V64H384V24c0-13.3-10.7-24-24-24s-24 10.7-24 24V64H152V24zM48 192h80v80H48V192zm112 0h80v80H160V192zm112 0h80v80H272V192zm-224 16v32c0 8.8 7.2 16 16 16h32c8.8 0 16-7.2 16-16V208c0-8.8-7.2-16-16-16H64c-8.8 0-16 7.2-16 16zm112 0v32c0 8.8 7.2 16 16 16h32c8.8 0 16-7.2 16-16V208c0-8.8-7.2-16-16-16H176c-8.8 0-16 7.2-16 16zm112 0v32c0 8.8 7.2 16 16 16h32c8.8 0 16-7.2 16-16V208c0-8.8-7.2-16-16-16H288c-8.8 0-16 7.2-16 16z"/></svg>
                    Mulai: ${startTime}
                </p>
            `;
            return card;
        }

        // Fungsi utama untuk memuat data dari API
        async function loadAgenda() {
            try {
                // Ganti dengan token otentikasi yang benar dari aplikasi Kodular Anda
                const authToken = '4|pTY5ELEX9zEdm18gxoqsGkRHnL9afDBH2tghxWb18dd0bc5d'; // Placeholder

                const response = await fetch(API_URL, {
                    headers: {
                        'Authorization': `Bearer ${authToken}`,
                        'Accept': 'application/json'
                    }
                });

                if (!response.ok) {
                    throw new Error(`Gagal mengambil data: ${response.statusText}`);
                }

                const result = await response.json();

                if (result.success) {
                    const { berlangsung, akan_datang } = result.data;

                    // Tampilkan agenda yang sedang berlangsung
                    listBerlangsung.innerHTML = '';
                    if (berlangsung.length > 0) {
                        berlangsung.forEach(event => listBerlangsung.appendChild(createAgendaCard(event, true)));
                    } else {
                        listBerlangsung.innerHTML = '<p class="info-kosong">Tidak ada agenda yang sedang berlangsung.</p>';
                    }

                    // Tampilkan agenda yang akan datang
                    listAkanDatang.innerHTML = '';
                    if (akan_datang.length > 0) {
                        akan_datang.forEach(event => listAkanDatang.appendChild(createAgendaCard(event)));
                    } else {
                        listAkanDatang.innerHTML = '<p class="info-kosong">Tidak ada agenda yang akan datang.</p>';
                    }
                }
            } catch (error) {
                console.error("Error:", error);
                listBerlangsung.innerHTML = '<p class="info-kosong">Gagal memuat data agenda.</p>';
            } finally {
                loadingIndicator.style.display = 'none';
            }
        }

        // Panggil fungsi utama saat halaman dimuat
        document.addEventListener('DOMContentLoaded', loadAgenda);
    </script>
</body>

</html>