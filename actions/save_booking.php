<?php
include '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $trip_id = $_POST['trip_id'];
    $customer_phone = $_POST['customer_phone'];
    $net_amount_total = $_POST['net_amount'];
    $passenger_names = $_POST['passenger_names']; 
    $num_seats = count($passenger_names);
    $price_per_seat = $net_amount_total / $num_seats;
    
    $group_id = 'GRP' . time() . rand(10, 99);

    try {
        $conn->beginTransaction();

        // ຫາເລກບ່ອນນັ່ງລ່າສຸດ (Lock table ເພື່ອປ້ອງກັນການຈອງຊ້ຳໃນເວລາດຽວກັນ)
        $seat_query = $conn->prepare("SELECT MAX(seat_number) as last_seat FROM Bookings WHERE trip_id = ?");
        $seat_query->execute([$trip_id]);
        $row = $seat_query->fetch();
        $current_seat = ($row && $row['last_seat']) ? intval($row['last_seat']) : 0;

        foreach ($passenger_names as $name) {
            $current_seat++; 
            $sql = "INSERT INTO Bookings (trip_id, customer_name, customer_phone, net_amount, booking_status, seat_number, group_id, created_at) 
                    VALUES (?, ?, ?, ?, 'Paid', ?, ?, NOW())";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$trip_id, $name, $customer_phone, $price_per_seat, $current_seat, $group_id]);
        }
        
        $update_sql = "UPDATE Trips SET available_seats = available_seats - ? WHERE trip_id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->execute([$num_seats, $trip_id]);

        $conn->commit();

        echo "<script>
            alert('ບັນທຶກການຈອງສຳເລັດ $num_seats ບ່ອນນັ່ງ!');
            window.location.href = '../print_ticket.php?group_id=$group_id';
        </script>";

    } catch (Exception $e) {
        $conn->rollBack();
        die("Error: " . $e->getMessage());
    }
}