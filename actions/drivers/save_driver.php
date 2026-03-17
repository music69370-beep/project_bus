<?php
include '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['full_name'];
    $phone = $_POST['phone'];
    $license = $_POST['license_number'];

    try {
        $sql = "INSERT INTO Drivers (full_name, phone, license_number) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$name, $phone, $license]);
        header("Location: ../manage_drivers.php?msg=success");
    } catch (PDOException $e) {
        header("Location: ../manage_drivers.php?msg=error");
    }
}
?>ຂ