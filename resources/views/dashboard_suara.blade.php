<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Suara</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; margin: 0; padding: 15px; background-color: #f0f2f5; }
        .filter-container { display: flex; gap: 20px; justify-content: center; align-items: center; padding: 15px; background-color: #fff; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 20px; flex-wrap: wrap; }
        .filter-item { display: flex; flex-direction: column; }
        .filter-item label { font-size: 12px; color: #606770; margin-bottom: 4px; }
        .filter-item select, #btnTerapkanFilter { padding: 8px 12px; border-radius: 6px; border: 1px solid #ccd0d5; font-size: 14px; }
        #btnTerapkanFilter { cursor: pointer; background-color: #1877f2; color: white; border: none; align-self: flex-end; }
        .chart-container { width: 100%; max-width: 800px; margin: auto; background-color: #ffffff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        h2 { text-align: center; color: #1c1e21; }
        #breadcrumb a { color: #1877f2; text-decoration: none; cursor: pointer; }
        #breadcrumb a:hover { text-decoration: underline; }
    </style>
</head>

<body>
    <div class="filter-container">
        <div class="filter-item">
            <label for="filterTahun">Pilih Tahun:</label>
            <select id="filterTahun">
                <option value="2024" selected>2024</option>
                <option value="2019">2019</option>
            </select>
        </div>
        <div class="filter-item">
            <label for="filterJenisSuara">Jenis Suara:</label>
            <select id="filterJenisSuara">
                <option value="dprd" selected>DPRD</option>
                <option value="dpr_prov">DPR PROV</option>
                <option value="dpr_ri">DPR RI</option>
            </select>
        </div>
        <button id="btnTerapkanFilter">Tampilkan</button>
    </div>

    <div class="chart-container">
        <h2 id="judul-perbandingan">Memuat data perbandingan...</h2>
        <canvas id="grafikPerbandinganSuara"></canvas>
    </div>

    <div class="chart-container" style="margin-top: 30px;">
        <h2 id="judul-detail">Memuat data detail...</h2>
        <div id="breadcrumb" style="text-align: center; margin-bottom: 15px; font-size: 14px;"></div>
        <div style="position: relative; height: 1200px;">
            <canvas id="grafikDetailKecamatan"></canvas>
        </div>
    </div>

    <script>
        const BASE_API_URL = '/api/dashboard/suara';
        const filterTahun = document.getElementById('filterTahun');
        const filterJenisSuara = document.getElementById('filterJenisSuara');
        const btnTerapkanFilter = document.getElementById('btnTerapkanFilter');
        const breadcrumbContainer = document.getElementById('breadcrumb');
        const perbandinganCanvas = document.getElementById('grafikPerbandinganSuara');
        const detailCanvas = document.getElementById('grafikDetailKecamatan');

        let breadcrumbs = [];

        async function initializeDashboard() {
            breadcrumbs = [{ level: 'kabupaten', id: null, label: 'Kabupaten Bogor' }];
            await Promise.all([
                loadGrafikPerbandingan(null),
                loadGrafikDetail(null, null)
            ]);
        }
        
        async function loadGrafikPerbandingan(idKecamatan) {
            let url = `${BASE_API_URL}/perbandingan`;
            if (idKecamatan) url += `?id_kecamatan=${idKecamatan}`;
            document.getElementById('judul-perbandingan').innerText = 'Memuat perbandingan...';
            try {
                const result = await (await fetch(url)).json();
                if (result.success) {
                    document.getElementById('judul-perbandingan').innerText = `Perbandingan Suara ${result.data.wilayah}`;
                    drawChartPerbandingan(result.data.data);
                }
            } catch (error) { console.error("Error Perbandingan:", error); }
        }

        function drawChartPerbandingan(data) {
            let chart = Chart.getChart(perbandinganCanvas);
            if (chart) chart.destroy();
            new Chart(perbandinganCanvas.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: ['DPRD', 'DPR PROV', 'DPR RI'],
                    datasets: [
                        { label: 'Suara 2019', data: [data.dprd['2019'], data.dpr_prov['2019'], data.dpr_ri['2019']], backgroundColor: 'rgba(255, 99, 132, 0.5)'},
                        { label: 'Suara 2024', data: [data.dprd['2024'], data.dpr_prov['2024'], data.dpr_ri['2024']], backgroundColor: 'rgba(54, 162, 235, 0.5)'}
                    ]
                },
                options: { responsive: true }
            });
        }

        async function loadGrafikDetail(idKecamatan, idDesa) {
            const tahun = filterTahun.value;
            let url = `${BASE_API_URL}/detail?tahun=${tahun}`;
            if (idKecamatan) url += `&id_kecamatan=${idKecamatan}`;
            if (idDesa) url += `&id_desa=${idDesa}`;
            
            document.getElementById('judul-detail').innerText = 'Memuat data detail...';
            updateBreadcrumb();
            
            try {
                const result = await (await fetch(url)).json();
                if (result.success) {
                    const jenisSuaraTeks = filterJenisSuara.options[filterJenisSuara.selectedIndex].text;
                    document.getElementById('judul-detail').innerText = `Peringkat Suara ${jenisSuaraTeks} (${tahun})`;
                    drawChartDetail(result.data);
                }
            } catch (error) { console.error("Error Detail:", error); }
        }

        function drawChartDetail(chartData) {
            let chart = Chart.getChart(detailCanvas);
            if (chart) chart.destroy();

            if (!chartData || !chartData.data || chartData.data.length === 0) {
                document.getElementById('judul-detail').innerHTML += `<br><small>Data Kosong</small>`;
                return;
            }

            const level = chartData.level;
            const jenisSuara = filterJenisSuara.value;
            const labels = chartData.data.map(item => item.kecamatan || item.desa || item.tps);
            const dataKey = (level === 'tps') ? jenisSuara : `total_${jenisSuara}`;
            const dataValues = chartData.data.map(item => item[dataKey] || 0);

            new Chart(detailCanvas.getContext('2d'), {
                type: 'bar',
                data: { labels, datasets: [{ data: dataValues, backgroundColor: 'rgba(75, 192, 192, 0.5)' }] },
                options: {
                    indexAxis: 'y', responsive: true, maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: { y: { ticks: { autoSkip: false } } },
                    onClick: (_, elements) => {
                        if (elements.length > 0) {
                            const itemData = chartData.data[elements[0].index];
                            if (level === 'kecamatan') {
                                breadcrumbs.push({ level: 'kecamatan', id: itemData.id_kecamatan, label: itemData.kecamatan });
                                loadGrafikDetail(itemData.id_kecamatan, null);
                                loadGrafikPerbandingan(itemData.id_kecamatan);
                            } else if (level === 'desa') {
                                breadcrumbs.push({ level: 'desa', id: itemData.id_desa, label: itemData.desa });
                                loadGrafikDetail(itemData.id_kecamatan, itemData.id_desa);
                            }
                        }
                    }
                }
            });
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
                    const target = breadcrumbs[breadcrumbs.length - 1];
                    if (target.level === 'kabupaten') {
                        initializeDashboard();
                    } else {
                        loadGrafikDetail(target.id, null);
                        loadGrafikPerbandingan(target.id);
                    }
                };
                breadcrumbContainer.appendChild(link);
            });
        }

        btnTerapkanFilter.addEventListener('click', initializeDashboard);
        document.addEventListener('DOMContentLoaded', initializeDashboard);
    </script>
</body>
</html>