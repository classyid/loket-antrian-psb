<?php session_start(); ?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8" />
<title>Dashboard Sistem Antrian Multi Loket</title>
<style>
  body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: #f0f4f8;
    color: #333;
    margin: 0; padding: 20px;
  }
  .container {
    max-width: 900px;
    margin: auto;
    background: white;
    border-radius: 12px;
    padding: 25px 30px;
    box-shadow: 0 6px 18px rgba(0,0,0,0.1);
  }
  h1 {
    text-align: center;
    color: #004a99;
    margin-bottom: 25px;
  }
  .loket-cards {
    display: flex;
    gap: 16px;
    flex-wrap: wrap;
    margin-bottom: 30px;
  }
  .loket-card {
    background: #f0f8ff;
    border-radius: 12px;
    box-shadow: 0 3px 12px rgba(0,0,0,0.1);
    padding: 20px;
    flex: 1 1 150px;
    text-align: center;
    font-weight: 600;
    color: #004a99;
    transition: box-shadow 0.3s ease;
  }
  .loket-card.active {
    background: #cce5ff;
    border: 2px solid #3399ff;
    box-shadow: 0 4px 15px rgba(51,153,255,0.5);
  }
  .loket-icon {
    font-size: 2.5rem;
    margin-bottom: 8px;
  }
  .loket-name {
    font-size: 1.25rem;
    margin-bottom: 6px;
  }
  .loket-number {
    font-size: 2.5rem;
    font-weight: 700;
    color: #0056d2;
  }
  .loket-status {
    margin-top: 6px;
    font-size: 1rem;
    color: #336699;
  }
  .stats {
    display: flex;
    justify-content: space-around;
    margin-bottom: 30px;
  }
  .stat-card {
    background: #007bff;
    color: white;
    border-radius: 10px;
    width: 28%;
    padding: 20px;
    box-shadow: 0 3px 8px rgba(0,123,255,0.4);
    text-align: center;
  }
  .stat-card h2 {
    margin: 10px 0 5px;
    font-size: 2.8rem;
  }
  .stat-card p {
    margin: 0;
    font-weight: 600;
    font-size: 1.1rem;
    letter-spacing: 0.05em;
  }
  table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 15px;
  }
  th, td {
    padding: 14px 18px;
    border-bottom: 1px solid #e1e7f0;
    text-align: center;
  }
  th {
    background: #007bff;
    color: white;
    text-transform: uppercase;
    font-weight: 600;
  }
  tbody tr:hover {
    background-color: #f0f8ff;
  }
  .btn {
    display: block;
    width: 220px;
    margin: 35px auto 0;
    padding: 15px;
    font-size: 1.2rem;
    color: #fff;
    background: #0056d2;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    text-align: center;
    text-decoration: none;
    box-shadow: 0 4px 15px rgba(0,86,210,0.5);
    transition: background-color 0.3s ease;
  }
  .btn:hover {
    background-color: #003f94;
  }
</style>
</head>
<body>
<div class="container">
  <h1>Dashboard Sistem Antrian Multi Loket</h1>

  <div class="loket-cards" id="loket-cards">Memuat data...</div>

  <div class="stats">
    <div class="stat-card">
      <h2 id="total-registrants">-</h2>
      <p>Total Pendaftar</p>
    </div>
    <div class="stat-card" style="background:#28a745; box-shadow: 0 3px 8px rgba(40,167,69,0.5);">
      <h2 id="served-count">-</h2>
      <p>Sudah Dilayani</p>
    </div>
    <div class="stat-card" style="background:#ffc107; box-shadow: 0 3px 8px rgba(255,193,7,0.5); color:#212529;">
      <h2 id="waiting-count">-</h2>
      <p>Menunggu</p>
    </div>
  </div>

  <h3>5 Pendaftar Berikutnya yang Akan Dipanggil</h3>
  <table>
    <thead>
      <tr>
        <th>Nomor Antrian</th>
        <th>Nama Pendaftar</th>
      </tr>
    </thead>
    <tbody id="next-registrants">
      <tr><td colspan="2" style="padding: 25px; font-style: italic;">Memuat data...</td></tr>
    </tbody>
  </table>

  <a href="login_loket.php" class="btn">Masuk ke Kontrol Loket</a>
</div>

<script>
const refreshIntervalMs = 3000;

// Ubah nama loket dari id ke nama user-friendly
function getLoketDisplayName(loketId) {
  const map = {
    'loket_1': 'Loket 1',
    'loket_2': 'Loket 2',
    'loket_3': 'Loket 3',
  };
  return map[loketId] || loketId;
}

// Tentukan status kartu loket berdasarkan apakah sedang aktif panggil nomor
function determineStatus(nomor) {
  return (nomor !== null) ? 'active' : 'waiting';
}

function renderLoketCards(loketData) {
  const container = document.getElementById('loket-cards');
  container.innerHTML = '';
  for (const [loketId, nomor] of Object.entries(loketData)) {
    const status = determineStatus(nomor);
    const card = document.createElement('div');
    card.className = 'loket-card' + (status === 'active' ? ' active' : '');
    card.innerHTML = `
      <div class="loket-icon">🏢</div>
      <div class="loket-name">${getLoketDisplayName(loketId)}</div>
      <div class="loket-number">${nomor !== null ? nomor : '-'}</div>
      <div class="loket-status">${status === 'active' ? 'Sedang Dipanggil' : 'Menunggu Panggil'}</div>
    `;
    container.appendChild(card);
  }
}

async function fetchStatus() {
  try {
    const response = await fetch('dashboard_api.php');
    if (!response.ok) throw new Error('Network error');
    const data = await response.json();

    renderLoketCards(data.nomor_per_loket);

    document.getElementById('total-registrants').textContent = data.total_registrants ?? '-';
    document.getElementById('served-count').textContent = data.served_count ?? '-';
    document.getElementById('waiting-count').textContent = data.waiting_count ?? '-';

    const tbody = document.getElementById('next-registrants');
    tbody.innerHTML = '';
    if (data.next_registrants && data.next_registrants.length > 0) {
      data.next_registrants.forEach(p => {
        const tr = document.createElement('tr');
        const tdNomor = document.createElement('td');
        tdNomor.textContent = p.nomor_antrian;
        const tdNama = document.createElement('td');
        tdNama.textContent = p.nama;
        tr.appendChild(tdNomor);
        tr.appendChild(tdNama);
        tbody.appendChild(tr);
      });
    } else {
      tbody.innerHTML = '<tr><td colspan="2" style="padding: 25px; font-style: italic;">Tidak ada pendaftar berikutnya.</td></tr>';
    }
  } catch (err) {
    console.error('Fetch status error:', err);
    const container = document.getElementById('loket-cards');
    container.textContent = 'Gagal memuat data.';
    const tbody = document.getElementById('next-registrants');
    tbody.innerHTML = '<tr><td colspan="2">Gagal memuat data.</td></tr>';
  }
}

setInterval(fetchStatus, refreshIntervalMs);
fetchStatus();
</script>

</body>
</html>
