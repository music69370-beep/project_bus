<?php
include '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['vehicle_id'];
    $plate = $_POST['plate_number'];
    $type = $_POST['vehicle_type'];
    $seats = $_POST['total_seats'];
    $status = $_POST['status'];

    try {
        // ຄຳສັ່ງ SQL ສຳລັບອັບເດດຂໍ້ມູນ
        $sql = "UPDATE Vehicles SET plate_number=?, vehicle_type=?, total_seats=?, status=? WHERE vehicle_id=?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$plate, $type, $seats, $status, $id]);

        // ຖ້າສຳເລັດ ໃຫ້ສົ່ງກັບໄປໜ້າຫຼັກພ້ອມ Parameter updated
        header("Location: ../manage_vehicles.php?msg=updated");
    } catch (PDOException $e) {
        // ຖ້າຜິດພາດ
        header("Location: ../manage_vehicles.php?msg=error");
    }
}
?>