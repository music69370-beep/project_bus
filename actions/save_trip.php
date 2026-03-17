<?php
include '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // ຮັບຄ່າ ແລະ ບັງຄັບໃຫ້ເປັນ Integer (ຕົວເລກ) ເພື່ອໃຫ້ກົງກັບ bigint
    $schedule_id = isset($_POST['schedule_id']) ? (int)$_POST['schedule_id'] : null;
    $vehicle_id  = isset($_POST['vehicle_id']) ? (int)$_POST['vehicle_id'] : null;
    $driver_id   = isset($_POST['driver_id']) ? (int)$_POST['driver_id'] : null;
    $trip_date   = $_POST['trip_date'] ?? null;

    if (!$schedule_id || !$vehicle_id || !$driver_id || !$trip_date) {
        die("Error: ຂໍ້ມູນບໍ່ຄົບຖ້ວນ!");
    }

    try {
        // 1. ດຶງຈຳນວນບ່ອນນັ່ງ (ກວດສອບກ່ອນວ່າລົດມີແທ້ບໍ່)
        $v_stmt = $conn->prepare("SELECT total_seats FROM Vehicles WHERE vehicle_id = ?");
        $v_stmt->execute([$vehicle_id]);
        $v = $v_stmt->fetch();
        
        if (!$v) {
            die("Error: ບໍ່ພົບລົດຄັນນີ້ໃນລະບົບ!");
        }
        $seats = (int)$v['total_seats'];

        // 2. ບັນທຶກຂໍ້ມູນ (Status ໃຊ້ຄ່າ Default ທີ່ເປັນ 'Pending' ຈາກ Database ເລີຍ)
        $sql = "INSERT INTO Trips (schedule_id, vehicle_id, driver_id, trip_date, available_seats) 
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$schedule_id, $vehicle_id, $driver_id, $trip_date, $seats]);

        // ຖ້າສຳເລັດ
        header("Location: ../manage_trips.php?msg=success");
        exit();

    } catch (PDOException $e) {
        // ຖ້າ Error ມັນຈະບອກເລີຍວ່າຕິດຂັດຢູ່ Column ໃດ
        echo "Database Error: " . $e->getMessage();
    }
}
?>