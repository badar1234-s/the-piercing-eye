<?php
require 'db.php';

$stmt = $pdo->query("SELECT id, username, full_name FROM users");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "<h1>Users List</h1>";
foreach ($users as $user) {
    echo "ID: {$user['id']} - Username: {$user['username']} - Full Name: {$user['full_name']}<br>";
}
?>
