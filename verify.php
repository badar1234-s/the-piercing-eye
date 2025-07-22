<?php
session_start();
require 'db.php'; // اتصال قاعدة البيانات

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

// تحقق من جواب سؤال التحقق البشري (مثلاً: 3 + 4 = 7)
if (!isset($_POST['human_check']) || trim($_POST['human_check']) !== '7') {
    echo "<p style='color: red; font-family: monospace;'>⚠️ عافاك أكد باللي نتي ماشي روبو. الجواب ديال سؤال التحقق خطأ.</p>";
    echo "<p><a href='index.php'>⏪ رجع لصفحة الدخول</a></p>";
    exit;
}

$username = trim($_POST['username']);
$password = $_POST['password'];

if ($username === '' || $password === '') {
    echo "عافاك عمر اسم المستخدم وكلمة السر. <a href='index.php'>رجع</a>";
    exit;
}

// منع هجمات brute force (بسيط)
if (!isset($_SESSION["attempts"])) {
    $_SESSION["attempts"] = 0;
}

if ($_SESSION["attempts"] >= 5) {
    echo "تعددت المحاولات. حاول مرة أخرى من بعد شوية.";
    exit;
}

// تحقق من المستخدم في قاعدة البيانات
$stmt = $pdo->prepare("SELECT id, password_hash, full_name, role FROM users WHERE username = ?");
$stmt->execute([$username]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user && password_verify($password, $user['password_hash'])) {
    // تسجيل الدخول ناجح
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $username;
    $_SESSION['full_name'] = $user['full_name'];
    $_SESSION['role'] = $user['role'];  // دور المستخدم
    $_SESSION["attempts"] = 0;
    $_SESSION['verified'] = true; // ✅ ضروري باش تخدم terminal.php

    // توجه حسب الدور
    if ($user['role'] === 'admin') {
        header('Location: admin.php');
    } else {
        header('Location: secret.php');
    }
    exit;
} else {
    $_SESSION["attempts"]++;
    echo "الدخول مرفوض. <a href='index.php'>رجع</a>";
}
