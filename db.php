<?php
// مسار قاعدة البيانات SQLite
$db_file = __DIR__ . '/database/database.db';

try {
    $pdo = new PDO("sqlite:" . $db_file);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec("PRAGMA foreign_keys = ON;");
} catch (PDOException $e) {
    error_log("❌ Database connection failed: " . $e->getMessage());
    http_response_code(500);
    exit;
}
?>
