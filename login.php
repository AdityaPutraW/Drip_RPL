<?php
include 'Database.php'; // Include the database connection file

session_start(); // Start the session

$error = ''; // Initialize error message

// Check if the form is submitted
if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Prepare and execute the query to check user credentials
    try {
        $query = "SELECT * FROM users WHERE username = :username";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();

        // Check if any user is found
        $user = $stmt->fetch(PDO::FETCH_ASSOC); // Fetch user data
        if ($user && $password == $user['password']) { // Untuk password plain text
            $_SESSION['login'] = true; // Set session variable
            header("Location: indeks.php"); // Redirect to the main page
            exit;
        } else {
            $error = "Username atau password salah!"; // Set error message
        }
    } catch (PDOException $e) {
        $error = "Error: " . $e->getMessage(); // Handle any errors
    }
}

// Close the connection
$conn = null; // For PDO, setting the connection to null closes it
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Login DRIP</title>
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
    .input, .button, .create-btn {
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
    .create-btn {
      background: #e7ebee;
      font-weight: bold;
      cursor: pointer;
      margin-top: 8px;
    }
    .create-btn:hover {
      background: #dfe4e7;
    }
    .error {
      color: red;
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
    (max-width: 700px) {
      .container { width: 95%; min-width: 0; padding: 20px 0; }
      .logo, .input, .button, .create-btn { width: 90%; }
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="logo">DRIP</div>
    <div class="title">Login Account</div>
    <form method="POST" action="">
      <div class="form-group">
        <input class="input" type="text" name="username" placeholder="Username" required>
        <input class="input" type="password" name="password" placeholder="Password" required>
        <button class="button" type="submit" name="login">Login</button>
        <button class="create-btn" type="button" onclick="window.location.href='register.php'">Create an Account</button>
      </div>
      <?php if ($error): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>
    </form>
  </div>
</body>
</html>
