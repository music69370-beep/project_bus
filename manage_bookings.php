<?php 
include 'config/db.php';
include 'includes/header.php';
include 'includes/sidebar.php';

// ດຶງຂໍ້ມູນການຈອງທັງໝົດ
$query = "SELECT b.*, t.trip_date, r.origin, r.destination, v.plate_number, s.departure_time 
          FROM Bookings b
          JOIN Trips t ON b.trip_id = t.trip_id
          JOIN Schedules s ON t.schedule_id = s.schedule_id
          JOIN Routes r ON s.route_id = r.route_id
          JOIN Vehicles v ON t.vehicle_id = v.vehicle_id
          ORDER BY b.created_at DESC";
$stmt = $conn->prepare($query);
$stmt->execute();
$bookings = $stmt->fetchAll();

// ດຶງຖ້ຽວລົດທີ່ວ່າງ
$trips = $conn->query("SELECT t.trip_id, t.trip_date, r.origin, r.destination, r.base_price_person, t.available_seats, s.departure_time 
                       FROM Trips t 
                       JOIN Schedules s ON t.schedule_id = s.schedule_id
                       JOIN Routes r ON s.route_id = r.route_id
                       WHERE t.available_seats > 0 AND t.status = 'Pending'")->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><i class="fas fa-ticket-alt text-success me-2"></i> ການຈອງປີ້</h1>
    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addBookingModal">
        <i class="fas fa-plus me-2"></i> ຈອງປີ້ໃໝ່
    </button>
</div>

<div class="card card-custom bg-white p-4 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover">
            <thead class="table-light">
                <tr>
                    <th>ຊື່ລູກຄ້າ</th>
                    <th>ຖ້ຽວລົດ / ເວລາອອກ</th>
                    <th>ວັນທີເດີນທາງ</th>
                    <th>ລາຄາທັງໝົດ</th>
                    <th>ສະຖານະ</th>
                    <th>ຈັດການ</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($bookings as $row): ?>
                <tr>
                    <td><strong><?php echo $row['customer_name']; ?></strong><br><small><?php echo $row['customer_phone']; ?></small></td>
                    <td>
                        <?php echo $row['origin']; ?> - <?php echo $row['destination']; ?><br>
                        <small class="text-primary"><i class="far fa-clock"></i> ເວລາ: <?php echo date('H:i', strtotime($row['departure_time'])); ?></small>
                    </td>
                    <td><?php echo date('d/m/Y', strtotime($row['trip_date'])); ?></td>
                    <td class="text-success font-weight-bold"><?php echo number_format($row['net_amount']); ?> ກີບ</td>
                    <td><span class="badge bg-success"><?php echo $row['booking_status']; ?></span></td>
                    <td>
                        <a href="print_ticket.php?id=<?php echo $row['booking_id']; ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-print"></i> ພິມປີ້</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="addBookingModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="actions/save_booking.php" method="POST">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">ບັນທຶກການຈອງປີ້</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">ເລືອກຖ້ຽວລົດ</label>
                        <select name="trip_id" class="form-control" required id="select_trip">
                            <option value="">-- ເລືອກຖ້ຽວ --</option>
                            <?php foreach($trips as $t): ?>
                                <option value="<?php echo $t['trip_id']; ?>" 
                                        data-price="<?php echo $t['base_price_person']; ?>"
                                        data-time="<?php echo date('H:i', strtotime($t['departure_time'])); ?>"
                                        data-seats="<?php echo $t['available_seats']; ?>">
                                    <?php echo $t['origin']; ?> - <?php echo $t['destination']; ?> 
                                    (<?php echo $t['departure_time']; ?>) - ວ່າງ: <?php echo $t['available_seats']; ?> ບ່ອນ
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div id="time_info" class="mt-2" style="display:none;">
                            <span class="badge bg-light text-primary border border-primary">
                                <i class="fas fa-clock me-1"></i> ເວລາອອກລົດ: <strong id="show_time"></strong>
                            </span>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">ຊື່ລູກຄ້າ</label>
                            <input type="text" name="customer_name" class="form-control" placeholder="ໃສ່ຊື່ລູກຄ້າ" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">ເບີໂທ</label>
                            <input type="text" name="customer_phone" class="form-control" placeholder="020..." required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">ລາຄາທີ່ຕ້ອງຈ່າຍ (ກີບ)</label>
                        <input type="number" name="net_amount" id="total_price" class="form-control bg-light" readonly>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ຍົກເລີກ</button>
                    <button type="submit" class="btn btn-success px-4">ຢືນຢັນການຈອງ ແລະ ຮັບເງິນ</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Logic ຈັດການເລືອກ Trip
document.getElementById('select_trip').addEventListener('change', function() {
    const opt = this.options[this.selectedIndex];
    const timeInfo = document.getElementById('time_info');
    const showTime = document.getElementById('show_time');
    const totalPrice = document.getElementById('total_price');

    if(!opt.value) {
        timeInfo.style.display = 'none';
        totalPrice.value = '';
        return;
    }

    const price = opt.getAttribute('data-price');
    const time = opt.getAttribute('data-time');
    const seats = opt.getAttribute('data-seats');

    totalPrice.value = price;
    showTime.innerText = time;
    timeInfo.style.display = 'block';

    if(parseInt(seats) <= 0) {
        Swal.fire({
            icon: 'error',
            title: 'ບ່ອນນັ່ງເຕັມ!',
            text: 'ຂໍອະໄພ, ຖ້ຽວລົດນີ້ເຕັມແລ້ວ.',
            confirmButtonColor: '#10b981'
        });
        this.value = "";
        timeInfo.style.display = 'none';
        totalPrice.value = '';
    }
});
</script>

<?php include 'includes/footer.php'; ?>