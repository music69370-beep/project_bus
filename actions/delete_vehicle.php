<?php
include '../config/db.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    try {
        // ໃຊ້ Soft Delete (ປ່ຽນ is_deleted ເປັນ 1) ເພື່ອຮັກສາປະຫວັດຂໍ້ມູນ
        $sql = "UPDATE Vehicles SET is_deleted = 1 WHERE vehicle_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$id]);

        header("Location: ../manage_vehicles.php?msg=deleted");
    } catch (PDOException $e) {
        header("Location: ../manage_vehicles.php?msg=error");
    }
}
?>