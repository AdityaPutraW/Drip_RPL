<?php
include 'Database.php';
session_start();

$error = '';
$success = '';

if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    header("Location: login.php");
    exit;
}

if (isset($_POST['tambah'])) {
    $nama_menu = $_POST['nama_menu'];
    $deskripsi = $_POST['deskripsi'];
    $harga = $_POST['harga'];

    if ($nama_menu == '' || $harga == '') {
        $error = "Nama menu dan harga wajib diisi!";
    } else {
        $query = "INSERT INTO menu (nama_menu, deskripsi, harga) VALUES (:nama_menu, :deskripsi, :harga)";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':nama_menu', $nama_menu);
        $stmt->bindParam(':deskripsi', $deskripsi);
        $stmt->bindParam(':harga', $harga);
        if ($stmt->execute()) {
            $success = "Menu berhasil ditambahkan!";
        } else {
            $error = "Gagal menambah menu!";
        }
    }
    $conn = null;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Tambah Menu</title>
  <style>
    body {
      background: #3e5056;
      font-family: Arial, sans-serif;
      margin: 0;
      padding: 0;
      height: 100vh;
      box-sizing: border-box;
    }
    .container {
      background: #a4b1b7;
      width: 500px;
      margin: 40px auto;
      border-radius: 20px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.08);
      padding: 40px 0;
      display: flex;
      flex-direction: column;
      align-items: center;
      position: relative;
      min-height: 400px;
    }
    .title {
      font-size: 28px;
      font-weight: bold;
      margin-bottom: 30px;
      color: #222;
      text-align: center;
    }
    .form-group {
      width: 100%;
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 18px;
      margin-bottom: 20px;
    }
    .input, .button {
      width: 320px;
      padding: 12px;
      border-radius: 8px;
      border: 2px solid #333;
      font-size: 18px;
      margin: 0 auto;
      box-sizing: border-box;
      background: #e7ebee;
      transition: background 0.2s;
    }
    .input:focus {
      background: #fff;
      outline: none;
    }
    .button {
      background: #e7ebee;
      font-weight: bold;
      cursor: pointer;
      margin-top: 8px;
    }
    .button:hover {
      background: #dfe4e7;
    }
    .error {
      color: red;
      margin-top: 10px;
      text-align: center;
      font-size: 16px;
    }
    .success {
      color: green;
      margin-top: 10px;
      text-align: center;
      font-size: 16px;
    }
    .back-link {
      margin-top: 20px;
      font-size: 16px;
      color: #333;
      text-decoration: underline;
      cursor: pointer;
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="title">Tambah Menu</div>
    <form method="POST" action="">
      <div class="form-group">
        <input class="input" type="text" name="nama_menu" placeholder="Nama Menu" required>
        <input class="input" type="text" name="deskripsi" placeholder="Deskripsi">
        <input class="input" type="number" name="harga" placeholder="Harga" required>
        <button class="button" type="submit" name="tambah">Tambah Menu</button>
      </div>
      <?php if ($error): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>
      <?php if ($success): ?>
        <div class="success"><?= htmlspecialchars($success) ?></div>
      <?php endif; ?>
    </form>
    <div class="back-link" onclick="window.location.href='indeks.php'">Kembali ke Dashboard</div>
  </div>
</body>
</html>