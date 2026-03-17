<?php
include '../config/db.php';
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    try {
        $stmt = $conn->prepare("DELETE FROM Routes WHERE route_id = ?");
        $stmt->execute([$id]);
        header("Location: ../manage_routes.php?msg=deleted");
    } catch (PDOException $e) {
        header("Location: ../manage_routes.php?msg=error");
    }
}