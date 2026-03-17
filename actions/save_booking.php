<?php
include '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $trip_id = $_POST['trip_id'];
    $customer_name = $_POST['customer_name'];
    $customer_phone = $_POST['customer_phone'];
    $net_amount = $_POST['net_amount'];

    try {
        $conn->beginTransaction(); // ເລີ່ມ Transaction ເພື່ອຄວາມປອດໄພຂອງຂໍ້ມູນ

        // 1. ບັນທຶກຂໍ້ມູນການຈອງ
        $sql1 = "INSERT INTO Bookings (trip_id, customer_name, customer_phone, net_amount, booking_status) VALUES (?, ?, ?, ?, 'Paid')";
        $stmt1 = $conn->prepare($sql1);
        $stmt1->execute([$trip_id, $customer_name, $customer_phone, $net_amount]);

        // 2. ຫຼຸດຈຳນວນບ່ອນນັ່ງວ່າງໃນ Trips (-1)
        $sql2 = "UPDATE Trips SET available_seats = available_seats - 1 WHERE trip_id = ?";
        $stmt2 = $conn->prepare($sql2);
        $stmt2->execute([$trip_id]);

        $conn->commit(); // ຢືນຢັນການປ່ຽນແປງທັງໝົດ
        header("Location: ../manage_bookings.php?msg=success");
    } catch (Exception $e) {
        $conn->rollBack(); // ຖ້າ Error ໃຫ້ຍົກເລີກທັງໝົດ
        header("Location: ../manage_bookings.php?msg=error");
    }
}
?>