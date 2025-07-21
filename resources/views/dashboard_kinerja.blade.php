<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Kinerja Struktur</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; margin: 0; padding: 15px; background-color: #f0f2f5; }
        .container { max-width: 900px; margin: auto; }
        .filter-container { display: flex; gap: 20px; justify-content: center; align-items: center; padding: 15px; background-color: #fff; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 20px; flex-wrap: wrap; }
        .filter-item label { font-size: 12px; color: #606770; margin-bottom: 4px; }
        .filter-item select { padding: 8px 12px; border-radius: 6px; border: 1px solid #ccd0d5; font-size: 14px; }
        #breadcrumb { text-align: left; margin-bottom: 15px; font-size: 16px; font-weight: 500; }
        #breadcrumb a { color: #fdb71fff; text-decoration: none; cursor: pointer; }
        #breadcrumb a:hover { text-decoration: underline; }
        .card-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 15px; }
        .card { background: #fff; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); padding: 20px; cursor: pointer; transition: transform 0.2s, box-shadow 0.2s; }
        .card:hover { transform: translateY(-5px); box-shadow: 0 4px 10px rgba(0,0,0,0.15); }
        .card-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 15px; }
        .card-title { font-size: 18px; font-weight: 600; color: #1c1e21; margin: 0; }
        .card-score { font-size: 24px; font-weight: 700; color: #116811ff; }
        .stat { display: flex; justify-content: space-between; font-size: 14px; padding: 8px 0; border-bottom: 1px solid #e9ebee; }
        .stat:last-child { border-bottom: none; }
        .stat-label { color: #606770; }
        .stat-value { font-weight: 500; color: #1c1e21; }
        .progress-bar { background-color: #e9ebee; border-radius: 5px; height: 10px; overflow: hidden; margin-top: 5px; }
        .progress { background-color: #116811ff; height: 100%; border-radius: 5px; transition: width 0.5s ease-in-out; }
        #loading-indicator { text-align: center; padding: 40px; font-size: 18px; color: #606770; }
    </style>
</head>
<body>
    <div class="container">
        <div class="filter-container">
            <div class="filter-item">
                <label for="filterSortBy">Urutkan Berdasarkan:</label>
                <select id="filterSortBy">
                    <option value="pencapaian" selected>Pencapaian Struktur</option>
                    <option value="anggota">Jumlah Anggota</option>
                </select>
            </div>
        </div>

        <div id="breadcrumb"></div>
        <div id="card-grid-container" class="card-grid"></div>
        <div id="loading-indicator" style="display: none;">Memuat data...</div>
    </div>

    <script>
        const BASE_API_URL = '/api/dashboard/kinerja';
        const sortByFilter = document.getElementById('filterSortBy');
        const cardContainer = document.getElementById('card-grid-container');
        const breadcrumbContainer = document.getElementById('breadcrumb');
        const loadingIndicator = document.getElementById('loading-indicator');

        let breadcrumbs = [{ level: 'kabupaten', id: null, label: 'Tingkat Kabupaten' }];

        async function fetchData(url) {
            loadingIndicator.style.display = 'block';
            cardContainer.innerHTML = '';
            try {
                const response = await fetch(url);
                if (!response.ok) throw new Error(`HTTP Error: ${response.status}`);
                const result = await response.json();
                return result.success ? result.data : [];
            } catch (error) {
                console.error('Fetch Error:', error);
                cardContainer.innerHTML = '<p>Gagal memuat data.</p>';
                return [];
            } finally {
                loadingIndicator.style.display = 'none';
            }
        }

        function createCard(item) {
            const card = document.createElement('div');
            card.className = 'card';
            card.onclick = () => {
                // Hanya lakukan drill-down jika yang diklik adalah kartu kecamatan
                if (!item.hasOwnProperty('persentase_dprt')) return; // Jika ini kartu desa, jangan lakukan apa-apa
                if (breadcrumbs.length === 1) { // Cek jika kita ada di level kabupaten
                     breadcrumbs.push({ level: 'kecamatan', id: item.id_wilayah, label: item.nama_wilayah });
                     loadDashboard();
                }
            };

            const persentase_dprt = item.terbentuk_dprt > 0 ? `(${(item.terbentuk_dprt / item.target_dprt * 100).toFixed(0)}%)` : '(0%)';

            card.innerHTML = `
                <div class="card-header">
                    <div>
                        <h3 class="card-title">${item.nama_wilayah}</h3>
                        <small class="stat-label">Total Anggota: ${item.total_anggota.toLocaleString('id-ID')}</small>
                    </div>
                    <div class="card-score">${item.skor_pencapaian}%</div>
                </div>
                <div>
                    <div class="stat">
                        <span class="stat-label">DPRt Terbentuk</span>
                        <span class="stat-value">${item.terbentuk_dprt} / ${item.target_dprt} ${persentase_dprt}</span>
                    </div>
                    <div class="progress-bar"><div class="progress" style="width: ${item.persentase_dprt}%;"></div></div>
                    <div class="stat">
                        <span class="stat-label">KORW Terbentuk</span>
                        <span class="stat-value">${item.terbentuk_korw} / ${item.target_korw}</span>
                    </div>
                     <div class="progress-bar"><div class="progress" style="width: ${item.persentase_korw}%;"></div></div>
                    <div class="stat">
                        <span class="stat-label">KORT Terbentuk</span>
                        <span class="stat-value">${item.terbentuk_kort} / ${item.target_kort}</span>
                    </div>
                     <div class="progress-bar"><div class="progress" style="width: ${item.persentase_kort}%;"></div></div>
                </div>
            `;
            return card;
        }

        function updateBreadcrumb() {
            breadcrumbContainer.innerHTML = '';
            breadcrumbs.forEach((crumb, index) => {
                if (index > 0) breadcrumbContainer.innerHTML += ` &nbsp;&gt;&nbsp; `;
                const link = document.createElement('a');
                link.innerText = crumb.label;
                link.onclick = (e) => {
                    e.preventDefault();
                    breadcrumbs = breadcrumbs.slice(0, index + 1);
                    loadDashboard();
                };
                breadcrumbContainer.appendChild(link);
            });
        }

        async function loadDashboard() {
            updateBreadcrumb();
            const currentCrumb = breadcrumbs[breadcrumbs.length - 1];
            let url = `${BASE_API_URL}?sort_by=${sortByFilter.value}`;
            if (currentCrumb.level === 'kecamatan') {
                url += `&id_kecamatan=${currentCrumb.id}`;
            }

            const data = await fetchData(url);
            data.forEach(item => {
                cardContainer.appendChild(createCard(item));
            });
        }

        sortByFilter.addEventListener('change', loadDashboard);
        document.addEventListener('DOMContentLoaded', loadDashboard);
    </script>
</body>
</html>