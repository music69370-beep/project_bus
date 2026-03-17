<?php
include '../config/db.php';
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $conn->prepare("DELETE FROM Trips WHERE trip_id = ?");
    if ($stmt->execute([$id])) {
        header("Location: ../manage_trips.php?msg=deleted");
    }
}