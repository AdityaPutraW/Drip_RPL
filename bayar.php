<?php
// Koneksi database menggunakan PDO
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

    // Ambil data menu dari database
    $stmt = $conn->prepare("SELECT nama_menu, harga FROM menu WHERE id_menu = ?");
    $stmt->execute([$id_menu]);
    $menu = $stmt->fetch();

    if ($menu) {
        $total = $menu['harga'] * $jumlah;
        $nama_menu = $menu['nama_menu'];

        // Simpan ke tabel transaksi
        $stmt = $conn->prepare("INSERT INTO transaksi (tanggal, nama, status, deskripsi) VALUES (?, ?, ?, ?)");
        $stmt->execute([$tanggal, $nama, $status, $nama_menu]);

        // Simpan ke tabel pembayaran
        $stmt = $conn->prepare("INSERT INTO pembayaran (tanggal, nama, status, total) VALUES (?, ?, ?, ?)");
        $stmt->execute([$tanggal, $nama, $status, $total]);

        echo "<p style='color:green;'>✅ Pembayaran berhasil! Total: Rp " . number_format($total, 0, ',', '.') . "</p>";
    } else {
        echo "<p style='color:red;'>❌ Menu tidak ditemukan.</p>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Form Pembayaran</title>
</head>
<body>
    <h2>Form Pembayaran</h2>
    <form method="post">
        Nama Pelanggan:<br>
        <input type="text" name="nama" required><br><br>

        Pilih Menu:<br>
        <select name="menu" required>
            <?php foreach ($menuList as $menu): ?>
                <option value="<?= $menu['id_menu'] ?>">
                    <?= $menu['nama_menu'] ?> (Rp <?= number_format($menu['harga'], 0, ',', '.') ?>)
                </option>
            <?php endforeach; ?>
        </select><br><br>

        Jumlah:<br>
        <input type="number" name="jumlah" value="1" min="1" required><br><br>

        <button type="submit">Bayar</button>
        <a href="indeks.php" style="margin-left: 10px; text-decoration: none;">
            <button type="button">Kembali</button>
        </a>
    </form>
</body>
</html>
