<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Code Absensi: {{ $event->title }}</title>
    <style>
        body, html { margin: 0; padding: 0; display: flex; justify-content: center; align-items: center; height: 100vh; background-color: #f0f2f5; font-family: sans-serif; }
        .container { text-align: center; background: #fff; padding: 40px; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
        h1 { color: #00573d; margin-top: 0; }
        p { color: #555; margin-bottom: 20px; }
        #qrcode-container { width: 400px; height: 400px; display: flex; justify-content: center; align-items: center; border: 1px solid #eee; }
        #qrcode-container p { color: #888; }
    </style>
</head>
<body>
    <div class="container">
        <h1>{{ $event->title }}</h1>
        <p>Pindai kode ini untuk mencatat kehadiran Anda.</p>
        <div id="qrcode-container">
            <p>Memuat QR Code...</p>
        </div>
    </div>

    <script>
        const QR_API_URL = `/api/events/{{ $event->id }}/qrcode`;
        const qrContainer = document.getElementById('qrcode-container');

        // PENTING: Ganti dengan token admin yang valid
        const ADMIN_AUTH_TOKEN = '1|Qmrlr0ZrGsCTMUlRhUCLwr9BToWPsmE7NiZIJiq026a03c0c';

        async function loadQrCode() {
            try {
                const response = await fetch(QR_API_URL, {
                    headers: {
                        'Authorization': `Bearer ${ADMIN_AUTH_TOKEN}`,
                        'Accept': 'image/svg+xml'
                    }
                });
                if (!response.ok) throw new Error('Gagal memuat QR Code');

                const svgData = await response.text();
                qrContainer.innerHTML = svgData;

            } catch (error) {
                console.error("Error:", error);
                qrContainer.innerHTML = '<p style="color: red;">Gagal memuat QR Code.</p>';
            }
        }

        document.addEventListener('DOMContentLoaded', loadQrCode);
    </script>
</body>
</html>
