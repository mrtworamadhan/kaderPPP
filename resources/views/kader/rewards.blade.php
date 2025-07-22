<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tukar Poin & Hadiah</title>
    <style>
        body, html { margin: 0; padding: 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #f0f2f5; color: #333; }
        .container { padding: 15px; }
        .header { padding: 10px 15px; background-color: #00573d; color: white; font-size: 20px; font-weight: 600; }
        .poin-summary { background: #fff; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); padding: 20px; text-align: center; margin-bottom: 20px; }
        .poin-summary .label { font-size: 16px; color: #606770; }
        .poin-summary .value { font-size: 36px; font-weight: 700; color: #00573d; margin-top: 5px; }
        .reward-list { display: grid; grid-template-columns: 1fr; gap: 15px; }
        .reward-card { background: #fff; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); overflow: hidden; }
        .reward-card img { width: 100%; height: 150px; object-fit: cover; background-color: #eee; }
        .reward-content { padding: 15px; }
        .reward-content h3 { margin: 0 0 5px; font-size: 16px; color: #004d35; }
        .reward-content .points { font-size: 14px; font-weight: 600; color: #f9a825; margin-bottom: 10px; }
        .reward-content .description { font-size: 13px; color: #555; margin-bottom: 15px; }
        .progress-bar { background-color: #e9ebee; border-radius: 5px; height: 8px; overflow: hidden; margin-bottom: 15px; }
        .progress { background-color: #28a745; height: 100%; border-radius: 5px; }
        .tukar-btn { width: 100%; background-color: #00573d; color: white; border: none; padding: 12px; border-radius: 6px; font-size: 14px; font-weight: 500; cursor: pointer; }
        .tukar-btn:disabled { background-color: #ccc; cursor: not-allowed; }
        #loading-indicator { text-align: center; padding: 40px; font-size: 16px; color: #606770; }
    </style>
</head>
<body>
    <div class="header">Tukar Poin & Hadiah</div>
    <div class="container">
        <div id="poin-summary" class="poin-summary" style="display: none;">
            <div class="label">Total Poin Anda</div>
            <div class="value">0</div>
        </div>
        <div id="reward-list" class="reward-list"></div>
        <div id="loading-indicator">Memuat hadiah...</div>
    </div>

    <script>
        const API_URL = '/api/rewards';
        const POIN_SUMMARY = document.getElementById('poin-summary');
        const REWARD_LIST = document.getElementById('reward-list');
        const LOADING_INDICATOR = document.getElementById('loading-indicator');

        function renderRewards(userPoin, rewards) {
            POIN_SUMMARY.querySelector('.value').innerText = userPoin.toLocaleString('id-ID');
            POIN_SUMMARY.style.display = 'block';
            REWARD_LIST.innerHTML = '';

            if (rewards.length === 0) {
                REWARD_LIST.innerHTML = '<p style="text-align:center;">Belum ada hadiah yang tersedia.</p>';
                return;
            }

            rewards.forEach(reward => {
                const percentage = Math.min(100, Math.round((userPoin / reward.points_needed) * 100));
                const canRedeem = userPoin >= reward.points_needed;
                const defaultImage = `https://placehold.co/600x300/00573d/FFFFFF?text=${reward.name}`;

                const card = document.createElement('div');
                card.className = 'reward-card';
                card.innerHTML = `
                    <img src="${reward.image || defaultImage}" alt="${reward.name}">
                    <div class="reward-content">
                        <h3>${reward.name}</h3>
                        <p class="points">${reward.points_needed.toLocaleString('id-ID')} Poin</p>
                        <p class="description">${reward.description || ''}</p>
                        <div class="progress-bar">
                            <div class="progress" style="width: ${percentage}%;"></div>
                        </div>
                        <button class="tukar-btn" ${!canRedeem ? 'disabled' : ''}>Tukar Poin</button>
                    </div>
                `;
                REWARD_LIST.appendChild(card);
            });
        }

        async function loadRewards(authToken) {
            if (!authToken) {
                LOADING_INDICATOR.innerText = 'Error: Token otentikasi tidak ditemukan.';
                return;
            }
            try {
                const response = await fetch(API_URL, {
                    headers: { 'Authorization': `Bearer ${authToken}`, 'Accept': 'application/json' }
                });
                if (!response.ok) throw new Error('Gagal memuat data rewards');
                
                const result = await response.json();
                if (result.success) {
                    renderRewards(result.data.total_poin_user, result.data.rewards);
                }
            } catch (error) {
                console.error("Error:", error);
                REWARD_LIST.innerHTML = '<p style="text-align:center; color:red;">Gagal memuat data.</p>';
            } finally {
                LOADING_INDICATOR.style.display = 'none';
            }
        }

        function start(token) {
            loadRewards(token);
        }

        document.addEventListener('DOMContentLoaded', () => {
             const TEST_TOKEN = '6|yuAL1icCCjvNkoOZhZ4L2kit07ixxHK3sUO8g1Ccc00ef44c';
             start(TEST_TOKEN);
        });
    </script>
</body>
</html>
