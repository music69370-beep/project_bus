<?php
include '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // ຮັບຄ່າຈາກ Form
    $plate_number = $_POST['plate_number'];
    $vehicle_type = $_POST['vehicle_type'];
    $total_seats  = $_POST['total_seats'];

    try {
        // ຄຳສັ່ງ SQL ເພື່ອບັນທຶກຂໍ້ມູນ
        $sql = "INSERT INTO Vehicles (plate_number, vehicle_type, total_seats, status) VALUES (?, ?, ?, 'Available')";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$plate_number, $vehicle_type, $total_seats]);

        // ຖ້າບັນທຶກສຳເລັດ ໃຫ້ກັບໄປໜ້າເກົ່າພ້ອມສົ່ງ Parameter success
        header("Location: ../manage_vehicles.php?msg=success");
    } catch (PDOException $e) {
        // ຖ້າຜິດພາດ
        header("Location: ../manage_vehicles.php?msg=error");
    }
}
?>