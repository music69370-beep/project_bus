<?php 
include 'config/db.php';
include 'includes/header.php';
include 'includes/sidebar.php';

// ດຶງຂໍ້ມູນຄົນຂັບ
$stmt = $conn->prepare("SELECT * FROM Drivers WHERE is_deleted = 0 ORDER BY driver_id DESC");
$stmt->execute();
$drivers = $stmt->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><i class="fas fa-user-tie text-success me-2"></i> ຈັດການຄົນຂັບລົດ</h1>
    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addDriverModal">
        <i class="fas fa-plus me-2"></i> ເພີ່ມຄົນຂັບໃໝ່
    </button>
</div>

<div class="card card-custom bg-white p-4 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover">
            <thead class="table-light">
                <tr>
                    <th>ລຳດັບ</th>
                    <th>ຊື່ ແລະ ນາມສະກຸນ</th>
                    <th>ເບີໂທລະສັບ</th>
                    <th>ເລກໃບຂັບຂີ່</th>
                    <th>ຈັດການ</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($drivers as $index => $row): ?>
                <tr>
                    <td><?php echo $index + 1; ?></td>
                    <td><?php echo $row['full_name']; ?></td>
                    <td><?php echo $row['phone']; ?></td>
                    <td><?php echo $row['license_number']; ?></td>
                    <td>
                        <button class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i></button>
                        <button class="btn btn-sm btn-outline-danger btn-delete" data-id="<?php echo $row['driver_id']; ?>"><i class="fas fa-trash"></i></button>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if(count($drivers) == 0) echo "<tr><td colspan='5' class='text-center'>ຍັງບໍ່ມີຂໍ້ມູນ</td></tr>"; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="addDriverModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="actions/save_driver.php" method="POST">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">ເພີ່ມຄົນຂັບໃໝ່</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">ຊື່ ແລະ ນາມສະກຸນ</label>
                        <input type="text" name="full_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">ເບີໂທລະສັບ</label>
                        <input type="text" name="phone" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">ເລກໃບຂັບຂີ່</label>
                        <input type="text" name="license_number" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">ບັນທຶກຂໍ້ມູນ</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>