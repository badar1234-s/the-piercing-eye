<?php
session_start();
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] === 'admin') {
        header('Location: admin.php');
    } else {
        header('Location: secret.php');
    }
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>The Piercing Eye - Login</title>
  <link rel="stylesheet" href="assets/style.css">
</head>
<body>
  <div class="gate">
    <h1>Access The Piercing Eye</h1>
    <form method="POST" action="verify.php">
      <input type="text" name="username" placeholder="Username" required>
      <input type="password" name="password" placeholder="Password" required>
      <input type="text" name="full_name" placeholder="Full Name" required>

      <!-- سؤال بشري ضد الروبوت -->
      <input type="text" name="human_check" placeholder="What is 3 + 4?" required>

      <button type="submit">Enter</button>
    </form>
    <p>Don't have an account? <a href="register.php">Register here</a></p>
  </div>
</body>
</html>
