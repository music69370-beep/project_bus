<?php
include '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $trip_id = $_POST['trip_id'];
    $customer_name = $_POST['customer_name'];
    $customer_phone = $_POST['customer_phone'];
    $net_amount = $_POST['net_amount'];

    try {
        $conn->beginTransaction();

        // 1. ບັນທຶກການຈອງ (created_at ຈະຖືກບັນທຶກອັດຕະໂນມັດຈາກ Database)
        $sql1 = "INSERT INTO Bookings (trip_id, customer_name, customer_phone, net_amount, booking_status, created_at) 
                VALUES (?, ?, ?, ?, 'Paid', NOW())";
        $stmt1 = $conn->prepare($sql1);
        $stmt1->execute([$trip_id, $customer_name, $customer_phone, $net_amount]);

        // 2. ຫຼຸດຈຳນວນບ່ອນນັ່ງ (-1)
        $sql2 = "UPDATE Trips SET available_seats = available_seats - 1 WHERE trip_id = ?";
        $stmt2 = $conn->prepare($sql2);
        $stmt2->execute([$trip_id]);

        $conn->commit();
        header("Location: ../manage_bookings.php?msg=success");
    } catch (Exception $e) {
        $conn->rollBack();
        header("Location: ../manage_bookings.php?msg=error");
    }
}
?>