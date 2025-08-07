<?php
session_start();
if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    header("Location: login.php");
    exit;
}
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: login.php");
    exit;
}
include 'Database.php';

// Ambil data menu dari database
$menus = [];
try {
    $stmt = $conn->query("SELECT * FROM menu");
    $menus = $stmt->fetchAll();
} catch (PDOException $e) {
    $menus = [];
}

// Ambil data transaksi dari database
$transaksis = [];
try {
    $stmt = $conn->query("SELECT * FROM transaksi");
    $transaksis = $stmt->fetchAll();
} catch (PDOException $e) {
    $transaksis = [];
}

// Ambil data pembayaran dari database
$pembayarans = [];
try {
    $stmt = $conn->query("SELECT * FROM pembayaran");
    $pembayarans = $stmt->fetchAll();
} catch (PDOException $e) {
    $pembayarans = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>DRIP Dashboard</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #3e5056;
      margin: 0;
      padding: 0;
      height: 100vh;
      box-sizing: border-box;
    }
    .container {
      display: flex;
      height: 100vh;
      box-sizing: border-box;
    }
    .sidebar {
      width: 250px;
      background: #a4b1b7;
      padding: 20px 0;
      display: flex;
      flex-direction: column;
      align-items: center;
      border-radius: 10px 0 0 10px;
      box-sizing: border-box;
    }
    .sidebar .logo {
      font-size: 28px;
      font-weight: bold;
      background: #fff;
      width: 80%;
      text-align: center;
      padding: 15px 0;
      border-radius: 5px;
      margin-bottom: 20px;
      border: 2px solid #333;
    }
    .sidebar .nav-btn {
      width: 80%;
      padding: 12px;
      margin: 10px 0;
      background: #dfe4e7;
      border: 2px solid #333;
      border-radius: 5px;
      font-size: 18px;
      cursor: pointer;
      text-align: left;
    }
    .sidebar .nav-btn.active {
      background: #fff;
      font-weight: bold;
    }
    .sidebar .logout {
      width: 80%;
      padding: 12px;
      margin-top: 30px;
      background: #e74c3c;
      color: #fff;
      border: none;
      border-radius: 5px;
      font-size: 18px;
      cursor: pointer;
    }
    .main-content {
      flex: 1;
      background: #bfcad1;
      padding: 30px 40px;
      border-radius: 0 10px 10px 0;
      box-sizing: border-box;
      display: flex;
      flex-direction: column;
      position: relative;
    }
    .top-bar {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 25px;
    }
    .section-title {
      font-size: 24px;
      font-weight: bold;
      color: #222;
    }
    .akun-btn {
      padding: 12px 30px;
      font-size: 18px;
      background: #fff;
      border: 2px solid #333;
      border-radius: 5px;
      cursor: pointer;
      font-weight: bold;
    }
    .menu-section {
      background: #e7ebee;
      border-radius: 10px;
      padding: 25px 30px;
      box-sizing: border-box;
      box-shadow: 0 2px 8px rgba(0,0,0,0.04);
      min-height: 400px;
      position: relative;
    }
    .menu-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 18px;
    }
    .tambah-btn {
      padding: 8px 18px;
      font-size: 16px;
      background: #fff;
      border: 2px solid #333;
      border-radius: 5px;
      cursor: pointer;
      font-weight: bold;
    }
    .menu-table {
      width: 100%;
      border-collapse: collapse;
      background: #f5f7fa;
      border-radius: 8px;
      overflow: hidden;
    }
    .menu-table th, .menu-table td {
      padding: 12px 10px;
      border: 1px solid #333;
      text-align: left;
      font-size: 16px;
    }
    .menu-table th {
      background: #dfe4e7;
      font-weight: bold;
    }
    .menu-table td {
      background: #f7f9fa;
    }
    .dashboard-card {
      background: #e7ebee;
      border: 2px solid #333;
      border-radius: 15px;
      width: 320px;
      height: 120px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 28px;
      font-weight: bold;
      color: #222;
      cursor: pointer;
      box-shadow: 0 2px 8px rgba(0,0,0,0.04);
      transition: background 0.2s;
    }
    .dashboard-card:hover {
      background: #dfe4e7;
    }
    @media (max-width: 900px) {
      .container { flex-direction: column; }
      .sidebar { width: 100%; border-radius: 10px 10px 0 0; flex-direction: row; justify-content: center; }
      .main-content { border-radius: 0 0 10px 10px; padding: 20px 10px; }
      .dashboard-card { width: 100%; height: 80px; font-size: 20px; }
      .main-content { padding: 10px; }
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="sidebar">
      <div class="logo">DRIP</div>
      <button class="nav-btn active" onclick="showSection('dashboard', this)">Dashboard</button>
      <button class="nav-btn" onclick="showSection('menu', this)">Menu</button>
      <button class="nav-btn" onclick="showSection('transaksi', this)">Transaksi</button>
      <button class="nav-btn" onclick="showSection('pembayaran', this)">Pembayaran</button>
      <button class="logout" onclick="window.location.href='?logout=1'">Logout</button>
    </div>
    <div class="main-content">
      <div class="top-bar">
        <div class="section-title" id="section-title">Dashboard</div>
        <button class="akun-btn">Akun</button>
      </div>
      <div id="dashboard" class="section">
        <div style="display: flex; justify-content: flex-end; margin-bottom: 10px;">
          <div style="font-size: 22px; font-weight: bold; color: #222; margin-right: 20px;">Tampilan Dashboard</div>
        </div>
        <div style="display: flex; justify-content: center; align-items: flex-start; gap: 20px; margin-top: 40px;">
          <div style="display: flex; flex-direction: column; gap: 20px;">
            <!-- <button class="dashboard-card" onclick="showSection('menu', document.querySelectorAll('.nav-btn')[1])">Menu</button> -->
            <button class="dashboard-card" onclick="showSection('pembayaran', document.querySelectorAll('.nav-btn')[3])">Pembayaran</button>
          </div>
          <button class="dashboard-card" style="align-self: flex-start;" onclick="showSection('transaksi', document.querySelectorAll('.nav-btn')[2])">Transaksi</button>
        </div>
      </div>
      <div id="menu" class="section" style="display:none;">
        <div class="menu-section">
          <div class="menu-header">
            <div class="section-title" style="margin:0;">Tampilan Menu</div>
            <button class="tambah-btn" onclick="window.location.href='tambah_menu.php'">Tambah Menu</button>
          </div>
          <table class="menu-table">
            <thead>
              <tr>
                <th>No</th>
                <th>Nama Menu</th>
                <th>Deskripsi</th>
                <th>Harga</th>
              </tr>
            </thead>
            <tbody>
              <?php if ($menus): ?>
                <?php foreach ($menus as $i => $menu): ?>
                  <tr>
                    <td><?= $i+1 ?></td>
                    <td><?= htmlspecialchars($menu['nama_menu']) ?></td>
                    <td><?= htmlspecialchars($menu['deskripsi']) ?></td>
                    <td>Rp <?= number_format($menu['harga'], 0, ',', '.') ?></td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr>
                  <td colspan="4" style="text-align:center;">Isi Menu</td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
      <div id="transaksi" class="section" style="display:none;">
        <div class="menu-section">
          <div class="section-title" style="margin-bottom:18px;">Tampilan Transaksi</div>
          <table class="menu-table">
            <thead>
              <tr>
                <th>No</th><th>Tanggal</th><th>Nama</th><th>Status</th><th>Deskripsi</th>
              </tr>
            </thead>
            <tbody>
              <?php if ($transaksis): ?>
                <?php foreach ($transaksis as $i => $trx): ?>
                  <tr>
                    <td><?= $i+1 ?></td>
                    <td><?= htmlspecialchars($trx['tanggal']) ?></td>
                    <td><?= htmlspecialchars($trx['nama']) ?></td>
                    <td><?= htmlspecialchars($trx['status']) ?></td>
                    <td><?= htmlspecialchars($trx['deskripsi']) ?></td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr><td colspan="5" style="text-align:center;">Data transaksi tidak tersedia.</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
      <div id="pembayaran" class="section" style="display:none;">
        <div class="menu-section">
          <div class="section-title" style="margin-bottom:18px;">Tampilan Pembayaran</div>
          <table class="menu-table">
            <thead>
              <tr>
                <th>No</th><th>Tanggal</th><th>Nama</th><th>Status</th><th>Total</th>
              </tr>
            </thead>
            <tbody>
              <?php if ($pembayarans): ?>
                <?php foreach ($pembayarans as $i => $pay): ?>
                  <tr>
                    <td><?= $i+1 ?></td>
                    <td><?= htmlspecialchars($pay['tanggal']) ?></td>
                    <td><?= htmlspecialchars($pay['nama']) ?></td>
                    <td><?= htmlspecialchars($pay['status']) ?></td>
                    <td>Rp <?= number_format($pay['total'], 0, ',', '.') ?></td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr><td colspan="5" style="text-align:center;">Data pembayaran tidak tersedia.</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
          <button class="tambah-btn" style="margin-top:20px;" onclick="window.location.href='bayar.php'">Bayar</button>
        </div>
      </div>
    </div>
  </div>
  <script>
    function showSection(id, btn) {
      document.querySelectorAll('.section').forEach(sec => sec.style.display = 'none');
      document.getElementById(id).style.display = 'block';
      document.getElementById('section-title').textContent =
        id === 'dashboard' ? 'Dashboard' :
        id === 'menu' ? 'Tampilan Menu' :
        id === 'transaksi' ? 'Tampilan Transaksi' :
        id === 'pembayaran' ? 'Tampilan Pembayaran' : '';
      document.querySelectorAll('.nav-btn').forEach(b => b.classList.remove('active'));
      if (btn) btn.classList.add('active');
    }
    // Tampilkan dashboard saat pertama kali
    showSection('dashboard', document.querySelectorAll('.nav-btn')[0]);
  </script>
</body>
</html>
