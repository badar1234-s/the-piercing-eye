<?php
require __DIR__ . '/../db.php';

try {
    $query = "ALTER TABLE users ADD COLUMN full_name TEXT";
    $pdo->exec($query);
    echo "Column 'full_name' added successfully.\n";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
