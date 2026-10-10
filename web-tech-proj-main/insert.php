<?php
session_start();
require_once 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Sanitize inputs
    $name      = trim(htmlspecialchars($_POST['name']));
    $location  = trim(htmlspecialchars($_POST['location']));
    $type      = trim(htmlspecialchars($_POST['type']));
    $price     = floatval($_POST['price']);
    $rating    = floatval($_POST['rating']);
    $region    = trim(htmlspecialchars($_POST['region']));
    $amenities = isset($_POST['amenities']) ? implode(',', $_POST['amenities']) : '';

    // Validate
    $errors = [];
    if (strlen($name) < 3)               $errors[] = "Property name must be at least 3 characters.";
    if (empty($location))                $errors[] = "Location is required.";
    if (empty($type))                    $errors[] = "Property type is required.";
    if ($price <= 0)                     $errors[] = "Price must be greater than 0.";
    if ($rating < 1 || $rating > 5)      $errors[] = "Rating must be between 1.0 and 5.0.";

    if (empty($errors)) {
        $sql = "INSERT INTO staynest_properties (name, location, type, price, rating, region, amenities)
                VALUES (:name, :location, :type, :price, :rating, :region, :amenities)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':name'      => $name,
            ':location'  => $location,
            ':type'      => $type,
            ':price'     => $price,
            ':rating'    => $rating,
            ':region'    => $region,
            ':amenities' => $amenities
        ]);
        $_SESSION['success'] = "✅ Property inserted! ID: " . $pdo->lastInsertId();
    } else {
        $_SESSION['error'] = implode("<br>", $errors);
    }

    header("Location: admin.php");
    exit();
}
?>