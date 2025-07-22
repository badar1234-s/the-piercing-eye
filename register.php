<?php
session_start();
require 'db.php'; // ملف اتصال بقاعدة البيانات

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $fullname = trim($_POST['fullname']);

    if ($username === '' || $password === '' || $fullname === '') {
        $error = "يرجى ملء جميع الحقول.";
    } else {
        // تحقق من أن الاسم غير موجود مسبقاً
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$username]);
        if ($stmt->fetch()) {
            $error = "اسم المستخدم مستخدم من قبل.";
        } else {
            // إدخال المستخدم مع كلمة مرور مشفرة
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (username, password_hash, full_name) VALUES (?, ?, ?)");
            $stmt->execute([$username, $hash, $fullname]);

            $_SESSION['user_id'] = $pdo->lastInsertId();
            $_SESSION['username'] = $username;
            $_SESSION['full_name'] = $fullname;
            header('Location: secret.php');
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ar">
<head>
  <meta charset="UTF-8" />
  <title>تسجيل حساب - The Piercing Eye</title>
  <link rel="stylesheet" href="assets/style.css" />
  <style>
    body {
      font-family: Arial, sans-serif;
      direction: rtl;
      background: #121212;
      color: #eee;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      margin: 0;
    }
    .gate {
      background: #222;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 0 10px #f39c12;
      width: 300px;
      text-align: center;
    }
    input {
      width: 90%;
      padding: 10px;
      margin: 10px 0;
      border-radius: 5px;
      border: none;
      font-size: 16px;
    }
    button {
      background: #f39c12;
      border: none;
      padding: 10px 20px;
      font-weight: bold;
      color: #121212;
      cursor: pointer;
      border-radius: 5px;
      width: 95%;
      font-size: 18px;
    }
    button:hover {
      background: #e67e22;
    }
    p {
      margin-top: 15px;
    }
    a {
      color: #f39c12;
      text-decoration: none;
    }
    a:hover {
      text-decoration: underline;
    }
    .error {
      color: #ff4c4c;
      margin-bottom: 15px;
    }
  </style>
</head>
<body>
  <div class="gate">
    <h1>إنشاء حساب جديد</h1>
    <?php if (isset($error)) : ?>
      <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>
    <form method="POST" action="">
      <input type="text" name="username" placeholder="اسم المستخدم" required value="<?= isset($username) ? htmlspecialchars($username) : '' ?>" />
      <input type="password" name="password" placeholder="كلمة المرور" required />
      <input type="text" name="fullname" placeholder="الاسم الكامل" required value="<?= isset($fullname) ? htmlspecialchars($fullname) : '' ?>" />
      <button type="submit">تسجيل</button>
    </form>
    <p>عندك حساب؟ <a href="index.php">سجل الدخول هنا</a></p>
  </div>
</body>
</html>
