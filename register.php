<?php
include 'Database.php';
session_start();

$error = '';
$success = '';

if (isset($_POST['register'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $query = "SELECT * FROM users WHERE username = :username";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':username', $username);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $error = "Username sudah digunakan!";
    } else {
        // Simpan akun baru
        $query = "INSERT INTO users (username, password) VALUES (:username, :password)";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':password', $password); 
        if ($stmt->execute()) {
            $success = "Akun berhasil dibuat! Silakan login.";
        } else {
            $error = "Gagal membuat akun!";
        }
    }
    $conn = null;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Register DRIP</title>
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
      width: 600px;
      margin: 40px auto;
      border-radius: 20px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.08);
      padding: 40px 0 40px 0;
      display: flex;
      flex-direction: column;
      align-items: center;
      position: relative;
      min-height: 500px;
    }
    .logo {
      background: #e7ebee;
      border: 3px solid #333;
      border-radius: 12px;
      width: 260px;
      height: 70px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 32px;
      font-weight: bold;
      margin-bottom: 40px;
      margin-top: 10px;
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
      padding: 14px;
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
    .top-right {
      position: absolute;
      top: 18px;
      right: 30px;
      font-size: 22px;
      font-weight: bold;
      color: #222;
    }
    @media (max-width: 700px) {
      .container { width: 95%; min-width: 0; padding: 20px 0; }
      .logo, .input, .button { width: 90%; }
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="logo">DRIP</div>
    <div class="title">Create Account</div>
    <form method="POST" action="">
      <div class="form-group">
        <input class="input" type="text" name="username" placeholder="Username" required>
        <input class="input" type="password" name="password" placeholder="Password" required>
        <button class="button" type="submit" name="register">Register</button>
      </div>
      <?php if ($error): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>
      <?php if ($success): ?>
        <div class="success"><?= htmlspecialchars($success) ?></div>
      <?php endif; ?>
    </form>
    <div style="margin-top:20px;">
      <a href="login.php" style="color:#333; text-decoration:underline; font-size:16px;">Kembali ke Login</a>
    </div>
  </div>
</body>
</html>
