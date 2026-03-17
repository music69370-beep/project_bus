<?php
include 'config/db.php';
// ດຶງ ID ຈາກ URL
$id = $_GET['id'] ?? 0;

// SQL Query ດຶງຂໍ້ມູນລາຍລະອຽດທັງໝົດຂອງການຈອງນັ້ນ
$query = "SELECT b.*, t.trip_date, r.origin, r.destination, v.plate_number, s.departure_time 
          FROM Bookings b
          JOIN Trips t ON b.trip_id = t.trip_id
          JOIN Schedules s ON t.schedule_id = s.schedule_id
          JOIN Routes r ON s.route_id = r.route_id
          JOIN Vehicles v ON t.vehicle_id = v.vehicle_id
          WHERE b.booking_id = ?";
$stmt = $conn->prepare($query);
$stmt->execute([$id]);
$ticket = $stmt->fetch();

if (!$ticket) { die("ບໍ່ພົບຂໍ້ມູນການຈອງ"); }
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Ticket #<?php echo $id; ?></title>
    <style>
        body { font-family: 'Phetsarath OT', 'Noto Sans Lao', sans-serif; padding: 20px; }
        .ticket { border: 2px solid #333; width: 350px; padding: 15px; margin: auto; border-radius: 10px; }
        .header { text-align: center; border-bottom: 2px solid #eee; padding-bottom: 10px; }
        .item { margin: 10px 0; display: flex; justify-content: space-between; }
        .footer { text-align: center; margin-top: 15px; font-size: 12px; color: #777; }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body>
    <div class="ticket">
        <div class="header">
            <h2>ປີ້ລົດໂດຍສານ</h2>
            <small>ເລກທີການຈອງ: #<?php echo $id; ?></small>
        </div>
        <div class="content">
            <div class="item"><span>ຊື່ລູກຄ້າ:</span> <strong><?php echo $ticket['customer_name']; ?></strong></div>
            <div class="item"><span>ເສັ້ນທາງ:</span> <strong><?php echo $ticket['origin']; ?>-<?php echo $ticket['destination']; ?></strong></div>
            <div class="item"><span>ວັນທີເດີນທາງ:</span> <strong><?php echo date('d/m/Y', strtotime($ticket['trip_date'])); ?></strong></div>
            <div class="item"><span>ເວລາອອກລົດ:</span> <strong><?php echo $ticket['departure_time']; ?></strong></div>
            <div class="item"><span>ທະບຽນລົດ:</span> <strong><?php echo $ticket['plate_number']; ?></strong></div>
            <hr>
            <div class="item"><span>ລາຄາທັງໝົດ:</span> <strong style="font-size: 1.2em; color: green;"><?php echo number_format($ticket['net_amount']); ?> ກີບ</strong></div>
        </div>
        <div class="footer">
            <p>ກະລຸນາມາກ່ອນເວລາ 30 ນາທີ<br>ຂອບໃຈທີ່ໃຊ້ບໍລິການ!</p>
        </div>
    </div>
    
    <div style="text-align: center; margin-top: 20px;" class="no-print">
        <button onclick="window.print()" style="padding: 10px 20px; background: green; color: white; border: none; border-radius: 5px; cursor: pointer;">
            <i class="fas fa-print"></i> ສັ່ງພິມປີ້
        </button>
        <button onclick="window.close()" style="padding: 10px 20px; background: #ccc; border: none; border-radius: 5px; cursor: pointer;">ປິດ</button>
    </div>
</body>
</html>