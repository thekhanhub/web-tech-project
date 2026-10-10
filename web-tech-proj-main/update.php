<?php
session_start();
require_once 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id       = intval($_POST['id']);
    $name     = trim(htmlspecialchars($_POST['name']));
    $location = trim(htmlspecialchars($_POST['location']));
    $type     = trim(htmlspecialchars($_POST['type']));
    $price    = floatval($_POST['price']);
    $rating   = floatval($_POST['rating']);
    $region   = trim(htmlspecialchars($_POST['region']));
    $amenities = isset($_POST['amenities']) ? implode(',', $_POST['amenities']) : '';

    $errors = [];
    if ($id <= 0)                    $errors[] = "Invalid record ID.";
    if (strlen($name) < 3)          $errors[] = "Name too short.";
    if ($price <= 0)                 $errors[] = "Invalid price.";
    if ($rating < 1 || $rating > 5) $errors[] = "Rating out of range.";

    if (empty($errors)) {
        $sql = "UPDATE staynest_properties
                SET name=:name, location=:location, type=:type,
                    price=:price, rating=:rating, region=:region, amenities=:amenities
                WHERE id=:id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':name'      => $name,
            ':location'  => $location,
            ':type'      => $type,
            ':price'     => $price,
            ':rating'    => $rating,
            ':region'    => $region,
            ':amenities' => $amenities,
            ':id'        => $id
        ]);
        $_SESSION['success'] = "✅ Record #$id updated successfully.";
    } else {
        $_SESSION['error'] = implode("<br>", $errors);
    }

    header("Location: admin.php");
    exit();
}
?>