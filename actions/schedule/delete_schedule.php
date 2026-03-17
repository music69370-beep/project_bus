<?php
include '../config/db.php';
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $conn->prepare("DELETE FROM Schedules WHERE schedule_id = ?");
    if ($stmt->execute([$id])) {
        header("Location: ../manage_schedules.php?msg=deleted");
    }
}