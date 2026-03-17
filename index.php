<?php 
include 'config/db.php';
include 'includes/header.php';
include 'includes/sidebar.php';

// --- 1. Query ດຶງຕົວເລກສະຫຼຸບ (ຕົວຢ່າງ Logic) ---

// ລາຍຮັບມື້ນີ້
$stmt1 = $conn->prepare("SELECT SUM(net_amount) as today_revenue FROM Bookings WHERE DATE(created_at) = CURDATE() AND booking_status = 'Paid'");
$stmt1->execute();
$rev = $stmt1->fetch();
$today_revenue = $rev['today_revenue'] ?? 0;

// ຈຳນວນຖ້ຽວລົດມື້ນີ້
$stmt2 = $conn->prepare("SELECT COUNT(trip_id) as today_trips FROM Trips WHERE trip_date = CURDATE()");
$stmt2->execute();
$trips = $stmt2->fetch();
$today_trips = $trips['today_trips'] ?? 0;

// ຈຳນວນລົດທັງໝົດ
$stmt3 = $conn->prepare("SELECT COUNT(vehicle_id) as total_vehicles FROM Vehicles WHERE is_deleted = 0");
$stmt3->execute();
$v_count = $stmt3->fetch();
$total_vehicles = $v_count['total_vehicles'] ?? 0;


?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">ພາບລວມລະບົບ (Dashboard)</h1>
    <div class="text-muted"><?php echo date('d/m/Y'); ?></div>
</div>

<div class="row">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card card-custom p-3 bg-white border-start border-4 border-success">
            <div class="d-flex align-items-center">
                <div class="icon-shape bg-light-success p-3 rounded-circle me-3" style="background-color: rgba(16, 185, 129, 0.1);">
                    <i class="fas fa-money-bill-wave text-success"></i>
                </div>
                <div>
                    <div class="text-muted small">ລາຍຮັບມື້ນີ້</div>
                    <div class="h5 mb-0 font-weight-bold"><?php echo number_format($today_revenue); ?> ກີບ</div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card card-custom p-3 bg-white border-start border-4 border-info">
            <div class="d-flex align-items-center">
                <div class="icon-shape bg-light-info p-3 rounded-circle me-3" style="background-color: rgba(59, 130, 246, 0.1);">
                    <i class="fas fa-bus text-info"></i>
                </div>
                <div>
                    <div class="text-muted small">ຖ້ຽວລົດມື້ນີ້</div>
                    <div class="h5 mb-0 font-weight-bold"><?php echo $today_trips; ?> ຖ້ຽວ</div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card card-custom p-3 bg-white border-start border-4 border-warning">
            <div class="d-flex align-items-center">
                <div class="icon-shape p-3 rounded-circle me-3" style="background-color: rgba(245, 158, 11, 0.1);">
                    <i class="fas fa-truck-moving text-warning"></i>
                </div>
                <div>
                    <div class="text-muted small">ລົດໃນລະບົບ</div>
                    <div class="h5 mb-0 font-weight-bold"><?php echo $total_vehicles; ?> ຄັນ</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-8 mb-4">
        <div class="card card-custom p-4 bg-white shadow-sm">
            <h5 class="mb-4"><i class="fas fa-chart-line text-success me-2"></i> ແນວໂນ້ມລາຍຮັບ 7 ວັນຜ່ານມາ</h5>
            <div style="height: 300px; position: relative;">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 mb-4">
    <div class="card card-custom p-4 bg-white shadow-sm">
        <h5 class="mb-4"><i class="fas fa-chart-pie text-success me-2"></i> ສະຖານະລົດ</h5>
        <div style="height: 300px; position: relative;">
            <canvas id="statusChart"></canvas>
        </div>
    </div>
</div>
</div>

<div class="row mt-4">
    <div class="col-12">
        <div class="card card-custom p-4 bg-white shadow-sm">
            <h5 class="mb-3"><i class="fas fa-history text-primary me-2"></i> ການຈອງລ່າສຸດ</h5>
            <div class="table-responsive">
                <table class="table table-borderless align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>ລູກຄ້າ</th>
                            <th>ເສັ້ນທາງ</th>
                            <th>ວັນທີເດີນທາງ</th>
                            <th>ລາຄາ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // ດຶງຂໍ້ມູນ 5 ລາຍການລ່າສຸດ
                        // ປ່ຽນ Query ບ່ອນ $stmt_recent ໃນ index.php
$stmt_recent = $conn->query("SELECT b.*, r.origin, r.destination, t.trip_date, s.departure_time 
                            FROM Bookings b 
                            JOIN Trips t ON b.trip_id = t.trip_id 
                            JOIN Schedules s ON t.schedule_id = s.schedule_id 
                            JOIN Routes r ON s.route_id = r.route_id 
                            ORDER BY b.created_at DESC LIMIT 5");

// ບ່ອນ Echo ໃນ tbody
while($row = $stmt_recent->fetch()) {
    echo "<tr>
            <td><strong>{$row['customer_name']}</strong></td>
            <td>{$row['origin']} - {$row['destination']} <br> <small class='text-primary'>ເວລາ: ".date('H:i', strtotime($row['departure_time']))."</small></td>
            <td>".date('d/m/Y', strtotime($row['trip_date']))."</td>
            <td class='text-success font-weight-bold'>".number_format($row['net_amount'])." ກີບ</td>
          </tr>";
}
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>