<?php 
include 'config/db.php';
include 'includes/header.php';
include 'includes/sidebar.php';

// 1. ດຶງຂໍ້ມູນຕາຕະລາງເວລາທັງໝົດມາໂຊໃນ Table
$stmt = $conn->prepare("SELECT s.*, r.origin, r.destination 
                        FROM Schedules s 
                        JOIN Routes r ON s.route_id = r.route_id 
                        ORDER BY r.origin ASC, s.departure_time ASC");
$stmt->execute();
$schedules = $stmt->fetchAll();

// 2. ດຶງຂໍ້ມູນເສັ້ນທາງມາໃຫ້ເລືອກໃນ Modal
$routes = $conn->query("SELECT * FROM Routes ORDER BY origin ASC")->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><i class="fas fa-clock text-success me-2"></i> ຈັດການເວລາອອກລົດ</h1>
    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addScheduleModal">
        <i class="fas fa-plus me-2"></i> ເພີ່ມເວລາອອກລົດໃໝ່
    </button>
</div>

<div class="card card-custom bg-white p-4 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover">
            <thead class="table-light">
                <tr>
                    <th>ລຳດັບ</th>
                    <th>ເສັ້ນທາງ (ຕົ້ນທາງ - ປາຍທາງ)</th>
                    <th>ເວລາອອກລົດ</th>
                    <th>ຈັດການ</th>
                </tr>
            </thead>
            <tbody>
                <?php if(count($schedules) > 0): ?>
                    <?php foreach($schedules as $index => $row): ?>
                    <tr>
                        <td><?php echo $index + 1; ?></td>
                        <td><strong><?php echo $row['origin']; ?></strong> <i class="fas fa-arrow-right mx-2 text-muted"></i> <strong><?php echo $row['destination']; ?></strong></td>
                        <td class="text-primary font-weight-bold"><?php echo date('H:i', strtotime($row['departure_time'])); ?></td>
                        <td>
                            <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i> ລຶບ</button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="text-center text-muted">ຍັງບໍ່ມີຂໍ້ມູນເວລາອອກລົດ</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="addScheduleModal" tabindex="-1" aria-labelledby="addScheduleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="actions/save_schedule.php" method="POST">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="addScheduleModalLabel">ເພີ່ມເວລາອອກລົດ</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">ເລືອກເສັ້ນທາງ</label>
                        <select name="route_id" class="form-control" required>
                            <option value="">-- ເລືອກເສັ້ນທາງ --</option>
                            <?php foreach($routes as $r): ?>
                                <option value="<?php echo $r['route_id']; ?>">
                                    <?php echo $r['origin']; ?> - <?php echo $r['destination']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">ເວລາອອກລົດ</label>
                        <input type="time" name="departure_time" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ຍົກເລີກ</button>
                    <button type="submit" class="btn btn-success">ບັນທຶກເວລາ</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>