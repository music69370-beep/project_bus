<?php 
include 'config/db.php';
include 'includes/header.php';
include 'includes/sidebar.php';

// 1. ປັບ SQL ໃຫ້ດຶງຂໍ້ມູນເວລາ ແລະ ເສັ້ນທາງໃຫ້ຄົບຖ້ວນ
$query = "SELECT t.*, r.origin, r.destination, v.plate_number, d.full_name, s.departure_time
          FROM Trips t
          LEFT JOIN Schedules s ON t.schedule_id = s.schedule_id
          LEFT JOIN Routes r ON s.route_id = r.route_id
          LEFT JOIN Vehicles v ON t.vehicle_id = v.vehicle_id
          LEFT JOIN Drivers d ON t.driver_id = d.driver_id
          WHERE t.status != 'Cancelled'
          ORDER BY t.trip_date DESC, s.departure_time ASC";
$stmt = $conn->prepare($query);
$stmt->execute();
$trips = $stmt->fetchAll();

// ດຶງຂໍ້ມູນ Routes, Vehicles, Drivers ເພື່ອໄປໃສ່ໃນ Select Box
$routes = $conn->query("SELECT r.route_id, r.origin, r.destination, s.schedule_id, s.departure_time 
                        FROM Routes r 
                        INNER JOIN Schedules s ON r.route_id = s.route_id
                        ORDER BY r.origin ASC")->fetchAll();

$vehicles = $conn->query("SELECT * FROM Vehicles WHERE status = 'Available' AND is_deleted = 0")->fetchAll();
$drivers = $conn->query("SELECT * FROM Drivers WHERE is_deleted = 0")->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><i class="fas fa-calendar-alt text-success-custom me-2"></i> ຈັດການຖ້ຽວເດີນທາງ</h1>
    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addTripModal">
        <i class="fas fa-plus me-2"></i> ເພີ່ມຖ້ຽວລົດ
    </button>
</div>

<div class="card card-custom bg-white p-4 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover">
            <thead class="table-light">
                <tr>
                    <th>ວັນທີ / ເວລາ</th>
                    <th>ເສັ້ນທາງ (ຕົ້ນທາງ-ປາຍທາງ)</th>
                    <th>ລົດ / ຄົນຂັບ</th>
                    <th>ບ່ອນນັ່ງວ່າງ</th>
                    <th>ສະຖານະ</th>
                    <th>ຈັດການ</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($trips as $row): ?>
                <tr>
                    <td>
                        <strong><?php echo date('d/m/Y', strtotime($row['trip_date'])); ?></strong><br>
                        <small class="text-primary"><i class="far fa-clock"></i> ອອກເວລາ: <?php echo date('H:i', strtotime($row['departure_time'])); ?></small>
                    </td>
                    <td><?php echo $row['origin']; ?> <i class="fas fa-arrow-right mx-1 text-muted"></i> <?php echo $row['destination']; ?></td>
                    <td>
                        <small><i class="fas fa-bus"></i> <?php echo $row['plate_number']; ?></small><br>
                        <small><i class="fas fa-user"></i> <?php echo $row['full_name']; ?></small>
                    </td>
                    <td class="text-center">
                        <?php if($row['available_seats'] > 0): ?>
                            <span class="badge bg-success"><?php echo $row['available_seats']; ?> ບ່ອນ</span>
                        <?php else: ?>
                            <span class="badge bg-danger">ເຕັມ</span>
                        <?php endif; ?>
                    </td>
                    <td><span class="badge bg-info text-dark"><?php echo $row['status']; ?></span></td>
                    <td>
                        <button onclick="confirmDelete(<?php echo $row['trip_id']; ?>, 'trip')" class="btn btn-sm btn-outline-danger">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="addTripModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-0">
            <form action="actions/save_trip.php" method="POST">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">ສ້າງຖ້ຽວລົດໃໝ່</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">ເລືອກເສັ້ນທາງ ແລະ ເວລາ</label>
                        <select name="schedule_id" class="form-control" required>
                            <option value="">-- ເລືອກເສັ້ນທາງ ແລະ ເວລາ --</option>
                            <?php foreach($routes as $r): ?>
                                <option value="<?php echo $r['schedule_id']; ?>">
                                    <?php echo $r['origin']; ?> - <?php echo $r['destination']; ?> (<?php echo $r['departure_time']; ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">ວັນທີເດີນທາງ</label>
                        <input type="date" name="trip_date" class="form-control" required min="<?php echo date('Y-m-d'); ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">ເລືອກລົດ</label>
                        <select name="vehicle_id" class="form-control" required>
                            <option value="">-- ເລືອກລົດ --</option>
                            <?php foreach($vehicles as $v): ?>
                                <option value="<?php echo $v['vehicle_id']; ?>">
                                    <?php echo $v['plate_number']; ?> (ວ່າງ <?php echo $v['total_seats']; ?> ບ່ອນ)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">ເລືອກຄົນຂັບ</label>
                        <select name="driver_id" class="form-control" required>
                            <option value="">-- ເລືອກຄົນຂັບ --</option>
                            <?php foreach($drivers as $d): ?>
                                <option value="<?php echo $d['driver_id']; ?>">
                                    <?php echo $d['full_name']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ຍົກເລີກ</button>
                    <button type="submit" class="btn btn-success px-4">ຢືນຢັນການສ້າງຖ້ຽວລົດ</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>