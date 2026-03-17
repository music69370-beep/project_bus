<?php
include '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $route_id = $_POST['route_id'];
    $departure_time = $_POST['departure_time'];

    try {
        $sql = "INSERT INTO Schedules (route_id, departure_time) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$route_id, $departure_time]);

        header("Location: ../manage_schedules.php?msg=success");
        exit();
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>