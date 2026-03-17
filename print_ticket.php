<?php
include 'config/db.php';

$group_id = $_GET['group_id'] ?? '';
$booking_id = $_GET['id'] ?? '';

// ດຶງຂໍ້ມູນ (ຮອງຮັບທັງແບບ ID ດ່ຽວ ແລະ Group ID)
$where = $group_id ? "b.group_id = ?" : "b.booking_id = ?";
$param = $group_id ?: $booking_id;

$query = "SELECT b.*, t.trip_date, r.origin, r.destination, v.plate_number, s.departure_time 
          FROM Bookings b
          JOIN Trips t ON b.trip_id = t.trip_id
          JOIN Schedules s ON t.schedule_id = s.schedule_id
          JOIN Routes r ON s.route_id = r.route_id
          JOIN Vehicles v ON t.vehicle_id = v.vehicle_id
          WHERE $where ORDER BY b.seat_number ASC";
$stmt = $conn->prepare($query);
$stmt->execute([$param]);
$tickets = $stmt->fetchAll();

if (!$tickets) die("ບໍ່ພົບຂໍ້ມູນ!");
?>

<!DOCTYPE html>
<html lang="lo">
<head>
    <meta charset="UTF-8">
    <title>ພິມປີ້</title>
    <style>
        body { font-family: 'Noto Sans Lao', sans-serif; margin: 0; background: #eee; }
        .ticket-page { 
            width: 80mm; background: #fff; padding: 15px; margin: 10px auto;
            border: 1px dashed #000; position: relative; box-sizing: border-box;
        }
        .header { text-align: center; border-bottom: 1px solid #000; margin-bottom: 10px; }
        .seat-box { font-size: 24px; font-weight: bold; border: 2px solid #000; text-align: center; margin: 10px 0; padding: 5px; }
        .info-row { display: flex; justify-content: space-between; font-size: 14px; margin-bottom: 3px; }
        .no-print { text-align: center; padding: 20px; }
        @media print {
            .no-print { display: none; }
            body { background: none; }
            .ticket-page { margin: 0; border: none; border-bottom: 1px dashed #000; page-break-after: always; width: 100%; }
        }
    </style>
</head>
<body onload="window.print()">

<div class="no-print">
    <button onclick="window.print()" style="padding:10px 20px; background:#10b981; color:#fff; border:none; border-radius:5px; cursor:pointer;">ພິມປີ້ທັງໝົດ</button>
    <a href="manage_bookings.php" style="margin-left:10px; text-decoration:none; color:blue;">ກັບຄືນ</a>
</div>

<?php foreach($tickets as $ticket): ?>
<div class="ticket-page">
    <div class="header">
        <h2 style="margin:0; font-size:18px;">ປີ້ລົດເມ - ຄິວລົດອັດສະລິຍະ</h2>
        <small>ID: #<?php echo $ticket['booking_id']; ?> (<?php echo $ticket['group_id']; ?>)</small>
    </div>

    <div class="seat-box">
        ບ່ອນນັ່ງ: <?php echo str_pad($ticket['seat_number'], 2, '0', STR_PAD_LEFT); ?>
    </div>

    <div class="info-row"><span>ເສັ້ນທາງ:</span> <strong><?php echo $ticket['origin']; ?> - <?php echo $ticket['destination']; ?></strong></div>
    <div class="info-row"><span>ວັນທີ:</span> <span><?php echo date('d/m/Y', strtotime($ticket['trip_date'])); ?></span></div>
    <div class="info-row"><span>ເວລາອອກ:</span> <strong style="font-size:16px;"><?php echo date('H:i', strtotime($ticket['departure_time'])); ?></strong></div>
    <div class="info-row"><span>ທະບຽນລົດ:</span> <span><?php echo $ticket['plate_number']; ?></span></div>
    
    <hr style="border: 0.5px solid #eee;">
    <div class="info-row"><span>ຊື່ຜູ້ໂດຍສານ:</span> <strong><?php echo $ticket['customer_name']; ?></strong></div>
    <div class="info-row"><span>ເບີໂທ:</span> <span><?php echo $ticket['customer_phone']; ?></span></div>
    <div class="info-row" style="margin-top:5px;"><span>ລາຄາ:</span> <strong style="color:red;"><?php echo number_format($ticket['net_amount']); ?> ກີບ</strong></div>

    <div style="text-align:center; margin-top:15px; font-size:11px; ">
        <p>ຂໍໃຫ້ທ່ານເດີນທາງດ້ວຍຄວາມປອດໄພ</p>
    </div>
</div>
<?php endforeach; ?>

</body>
</html>