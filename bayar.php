<?php
$host = 'localhost';
$db   = 'drip_db';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $conn = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    die('Koneksi database gagal: ' . $e->getMessage());
}

// Ambil daftar menu dari database
try {
    $menuList = $conn->query("SELECT * FROM menu")->fetchAll();
} catch (PDOException $e) {
    die('Gagal mengambil data menu: ' . $e->getMessage());
}

// Proses form jika disubmit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = $_POST['nama'];
    $id_menu = $_POST['menu'];
    $jumlah = (int) $_POST['jumlah'];
    $tanggal = date('Y-m-d');
    $status = 'berhasil';

    $stmt = $conn->prepare("SELECT nama_menu, harga FROM menu WHERE id_menu = ?");
    $stmt->execute([$id_menu]);
    $menu = $stmt->fetch();

    if ($menu) {
        $total = $menu['harga'] * $jumlah;
        $nama_menu = $menu['nama_menu'];

        $stmt = $conn->prepare("INSERT INTO transaksi (tanggal, nama, status, deskripsi) VALUES (?, ?, ?, ?)");
        $stmt->execute([$tanggal, $nama, $status, $nama_menu]);

        $stmt = $conn->prepare("INSERT INTO pembayaran (tanggal, nama, status, total) VALUES (?, ?, ?, ?)");
        $stmt->execute([$tanggal, $nama, $status, $total]);

        $pesan = "<p style='color:green; font-weight:bold;'>✅ Pembayaran berhasil! Total: Rp " . number_format($total, 0, ',', '.') . "</p>";
    } else {
        $pesan = "<p style='color:red; font-weight:bold;'>❌ Menu tidak ditemukan.</p>";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Form Pembayaran</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #455a64;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            background-color: #90a4ae;
            padding: 30px;
            border-radius: 15px;
            width: 350px;
            text-align: center;
            box-shadow: 0 0 10px rgba(0,0,0,0.2);
        }
        h1 {
            background-color: #cfd8dc;
            padding: 10px;
            border-radius: 10px;
            border: 2px solid #333;
            margin-bottom: 20px;
        }
        h2 {
            margin-bottom: 20px;
            color: #333;
        }
        input, select, button {
            width: 90%;
            padding: 10px;
            margin: 8px 0;
            border: 1px solid #333;
            border-radius: 8px;
            font-size: 14px;
        }
        button {
            background-color: #cfd8dc;
            font-weight: bold;
            cursor: pointer;
        }
        button:hover {
            background-color: #b0bec5;
        }
        .message {
            margin-top: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>DRIP</h1>
        <h2>Form Pembayaran</h2>

        <?php if (!empty($pesan)): ?>
            <div class="message"><?= $pesan ?></div>
        <?php endif; ?>

        <form method="post">
            <input type="text" name="nama" placeholder="Nama Pelanggan" required>
            
            <select name="menu" required>
                <?php foreach ($menuList as $menu): ?>
                    <option value="<?= $menu['id_menu'] ?>">
                        <?= $menu['nama_menu'] ?> (Rp <?= number_format($menu['harga'], 0, ',', '.') ?>)
                    </option>
                <?php endforeach; ?>
            </select>

            <input type="number" name="jumlah" value="1" min="1" required>

            <button type="submit">Bayar</button>
            <a href="indeks.php" style="text-decoration: none; display: block; margin-top: 5px;">
                <button type="button">Kembali</button>
            </a>
        </form>
    </div>
</body>
</html>
