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
                        <a href="print_ticket.php?group_id=<?php echo $ticket['group_id'] ?: $row['group_id']; ?>&id=<?php echo $row['booking_id']; ?>" target="_blank" class="btn btn-sm btn-outline-primary">
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
        <div class="modal-content border-0 shadow-lg">
            <form action="actions/save_booking.php" method="POST">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title"><i class="fas fa-ticket-alt me-2"></i> ບັນທຶກການຈອງປີ້ໃໝ່</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label font-weight-bold">ເລືອກຖ້ຽວລົດ</label>
                        <select name="trip_id" class="form-control" required id="select_trip">
                            <option value="">-- ເລືອກຖ້ຽວ --</option>
                            <?php foreach($trips as $t): ?>
                                <option value="<?php echo $t['trip_id']; ?>" 
                                        data-price="<?php echo $t['base_price_person']; ?>"
                                        data-seats="<?php echo $t['available_seats']; ?>">
                                    <?php echo $t['origin']; ?> - <?php echo $t['destination']; ?> 
                                    (<?php echo date('H:i', strtotime($t['departure_time'])); ?>) - ວ່າງ: <?php echo $t['available_seats']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-primary font-weight-bold">ຈຳນວນປີ້ (ບ່ອນນັ່ງ)</label>
                        <input type="number" name="num_seats" id="num_seats" class="form-control" value="1" min="1" required>
                    </div>

                    <label class="form-label small font-weight-bold text-success">ລາຍຊື່ຜູ້ໂດຍສານ:</label>
                    <div id="passenger_inputs" class="p-3 border rounded bg-light mb-3" style="max-height: 200px; overflow-y: auto;">
                        <div class="mb-2">
                            <label class="form-label small mb-1">ຊື່ຜູ້ໂດຍສານ 1 (ຫົວໜ້າກຸ່ມ)</label>
                            <input type="text" name="passenger_names[]" class="form-control form-control-sm" placeholder="ກອກຊື່ແທ້" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label font-weight-bold">ເບີໂທຕິດຕໍ່ (ໃຊ້ເບີດຽວກັນທັງກຸ່ມ)</label>
                        <input type="text" name="customer_phone" class="form-control" placeholder="020..." required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">ລາຄາລວມທັງໝົດ (ກີບ)</label>
                        <input type="number" name="net_amount" id="total_price" class="form-control bg-white text-danger fw-bold" style="font-size: 1.3rem;" readonly>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ຍົກເລີກ</button>
                    <button type="submit" class="btn btn-success px-4">ຢືນຢັນການຈອງ</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
(function() {
    const selectTrip = document.getElementById('select_trip');
    const numSeats = document.getElementById('num_seats');
    const container = document.getElementById('passenger_inputs');
    const totalPrice = document.getElementById('total_price');

    function updateUI() {
        const opt = selectTrip.options[selectTrip.selectedIndex];
        if(!opt || !opt.value) {
            totalPrice.value = '';
            return;
        }

        const price = parseFloat(opt.getAttribute('data-price'));
        const available = parseInt(opt.getAttribute('data-seats'));
        let count = parseInt(numSeats.value) || 1;

        if(count > available) {
            alert('ຂໍອະໄພ, ບ່ອນນັ່ງວ່າງເຫຼືອພຽງ ' + available + ' ບ່ອນ');
            count = available;
            numSeats.value = available;
        }

        container.innerHTML = '';
        for(let i = 1; i <= count; i++) {
            container.innerHTML += `
                <div class="mb-2">
                    <label class="form-label small mb-1">ຊື່ຜູ້ໂດຍສານ ${i} ${i==1 ? '(ຫົວໜ້າ)' : ''}</label>
                    <input type="text" name="passenger_names[]" class="form-control form-control-sm" placeholder="ກອກຊື່ແທ້..." required>
                </div>`;
        }
        totalPrice.value = Math.floor(count * price);
    }

    selectTrip.addEventListener('change', updateUI);
    numSeats.addEventListener('input', updateUI);
})();
</script>

<?php include 'includes/footer.php'; ?>