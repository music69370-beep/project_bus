<?php
include '../config/db.php';
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    try {
        $conn->beginTransaction();
        
        // ດຶງ trip_id ກ່ອນລຶບເພື່ອໄປບວກບ່ອນນັ່ງຄືນ
        $b = $conn->prepare("SELECT trip_id FROM Bookings WHERE booking_id = ?");
        $b->execute([$id]);
        $res = $b->fetch();
        
        if($res) {
            // 1. ລຶບ Booking
            $conn->prepare("DELETE FROM Bookings WHERE booking_id = ?")->execute([$id]);
            // 2. ບວກບ່ອນນັ່ງຄືນໃຫ້ Trip (+1)
            $conn->prepare("UPDATE Trips SET available_seats = available_seats + 1 WHERE trip_id = ?")->execute([$res['trip_id']]);
        }

        $conn->commit();
        header("Location: ../manage_bookings.php?msg=deleted");
    } catch (Exception $e) {
        $conn->rollBack();
        header("Location: ../manage_bookings.php?msg=error");
    }
} else {
    header("Location: ../manage_bookings.php?msg=invalid");
}