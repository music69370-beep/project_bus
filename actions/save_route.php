<?php
include '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $origin = $_POST['origin'];
    $destination = $_POST['destination'];
    $distance_km = $_POST['distance_km'];
    $price_p = $_POST['base_price_person'];
    $price_pkg = $_POST['base_price_parcel'];

    try {
        $sql = "INSERT INTO Routes (origin, destination, distance_km, base_price_person, base_price_parcel) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$origin, $destination, $distance_km, $price_p, $price_pkg]);

        header("Location: ../manage_routes.php?msg=success");
    } catch (PDOException $e) {
        header("Location: ../manage_routes.php?msg=error");
    }
}
?>