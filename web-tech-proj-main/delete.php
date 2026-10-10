<?php
session_start();
require_once 'db_connect.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    if ($id <= 0) {
        die("Invalid ID.");
    }

    $sql  = "DELETE FROM staynest_properties WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $id]);

    $_SESSION['success'] = "🗑️ Record #$id deleted. Rows affected: " . $stmt->rowCount();
    header("Location: admin.php");
    exit();
}
?>