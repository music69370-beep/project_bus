<?php 
include 'config/db.php';
include 'includes/header.php';
include 'includes/sidebar.php';

// ດຶງຂໍ້ມູນລົດທັງໝົດ
$stmt = $conn->prepare("SELECT * FROM Vehicles WHERE is_deleted = 0 ORDER BY vehicle_id DESC");
$stmt->execute();
$vehicles = $stmt->fetchAll();
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><i class="fas fa-bus text-success me-2"></i> ຈັດການຂໍ້ມູນລົດ</h1>
    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addVehicleModal">
        <i class="fas fa-plus me-2"></i> ເພີ່ມລົດໃໝ່
    </button>
</div>

<div class="card card-custom bg-white p-4 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover">
            <thead class="table-light">
                <tr>
                    <th>ລຳດັບ</th>
                    <th>ເລກທະບຽນ</th>
                    <th>ປະເພດລົດ</th>
                    <th>ຈຳນວນບ່ອນນັ່ງ</th>
                    <th>ສະຖານະ</th>
                    <th>ຈັດການ</th>
                </tr>
            </thead>
            <tbody>
                <?php if(count($vehicles) > 0): ?>
                    <?php foreach($vehicles as $index => $row): ?>
                    <tr>
                        <td><?php echo $index + 1; ?></td>
                        <td><span class="badge bg-light text-dark border p-2"><?php echo $row['plate_number']; ?></span></td>
                        <td><?php echo $row['vehicle_type']; ?></td>
                        <td><?php echo $row['total_seats']; ?> ບ່ອນນັ່ງ</td>
                        <td>
                            <?php if($row['status'] == 'Available'): ?>
                                <span class="badge bg-success">ວ່າງ</span>
                            <?php elseif($row['status'] == 'Maintenance'): ?>
                                <span class="badge bg-warning text-dark">ສ້ອມແປງ</span>
                            <?php else: ?>
                                <span class="badge bg-danger">ບໍ່ວ່າງ</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-outline-primary me-1 btn-edit" 
                                    data-id="<?php echo $row['vehicle_id']; ?>"
                                    data-plate="<?php echo $row['plate_number']; ?>"
                                    data-type="<?php echo $row['vehicle_type']; ?>"
                                    data-seats="<?php echo $row['total_seats']; ?>"
                                    data-status="<?php echo $row['status']; ?>">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger btn-delete" data-id="<?php echo $row['vehicle_id']; ?>">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">ຍັງບໍ່ມີຂໍ້ມູນລົດໃນລະບົບ</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="addVehicleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <form action="actions/save_vehicle.php" method="POST">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">ເພີ່ມຂໍ້ມູນລົດໃໝ່</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label font-weight-bold">ເລກທະບຽນ</label>
                        <input type="text" name="plate_number" class="form-control" placeholder="ຕົວຢ່າງ: ກນ 1234" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label font-weight-bold">ປະເພດລົດ</label>
                        <select name="vehicle_type" class="form-control" required>
                            <option value="ລົດເມ">ລົດເມ</option>
                            <option value="ລົດຕູ້">ລົດຕູ້</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label font-weight-bold">ຈຳນວນບ່ອນນັ່ງ</label>
                        <input type="number" name="total_seats" class="form-control" value="45" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">ຍົກເລີກ</button>
                    <button type="submit" class="btn btn-success px-4">ບັນທຶກຂໍ້ມູນ</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="editVehicleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <form action="actions/update_vehicle.php" method="POST">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">ແກ້ໄຂຂໍ້ມູນລົດ</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="vehicle_id" id="edit_id">
                    <div class="mb-3">
                        <label class="form-label font-weight-bold">ເລກທະບຽນ</label>
                        <input type="text" name="plate_number" id="edit_plate" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label font-weight-bold">ປະເພດລົດ</label>
                        <select name="vehicle_type" id="edit_type" class="form-control" required>
                            <option value="ລົດເມ">ລົດເມ</option>
                            <option value="ລົດຕູ້">ລົດຕູ້</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label font-weight-bold">ຈຳນວນບ່ອນນັ່ງ</label>
                        <input type="number" name="total_seats" id="edit_seats" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label font-weight-bold">ສະຖານະ</label>
                        <select name="status" id="edit_status" class="form-control">
                            <option value="Available">ວ່າງ</option>
                            <option value="Maintenance">ສ້ອມແປງ</option>
                            <option value="Busy">ບໍ່ວ່າງ</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">ຍົກເລີກ</button>
                    <button type="submit" class="btn btn-primary px-4">ບັນທຶກການແກ້ໄຂ</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

<script>
$(document).ready(function() {
    // 1. ແຈ້ງເຕືອນ SweetAlert ຕາມ Parameter msg
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('msg')) {
        const msg = urlParams.get('msg');
        if (msg === 'success') {
            Swal.fire({ icon: 'success', title: 'ສຳເລັດ!', text: 'ດຳເນີນການຮຽບຮ້ອຍແລ້ວ', confirmButtonColor: '#10b981' });
        } else if (msg === 'updated') {
            Swal.fire({ icon: 'success', title: 'ອັບເດດແລ້ວ!', text: 'ຂໍ້ມູນລົດຖືກແກ້ໄຂຮຽບຮ້ອຍ', confirmButtonColor: '#3b82f6' });
        } else if (msg === 'deleted') {
            Swal.fire({ icon: 'success', title: 'ລົບສຳເລັດ!', text: 'ຂໍ້ມູນຖືກຍ້າຍໄປຖັງຂີ້ເຫຍື້ອແລ້ວ', confirmButtonColor: '#ef4444' });
        } else if (msg === 'error') {
            Swal.fire({ icon: 'error', title: 'ຂໍ້ຜິດພາດ!', text: 'ບໍ່ສາມາດດຳເນີນການໄດ້', confirmButtonColor: '#ef4444' });
        }
        window.history.replaceState({}, document.title, window.location.pathname);
    }

    // 2. Logic ສຳລັບປຸ່ມລົບ
    $('.btn-delete').click(function() {
        const id = $(this).data('id');
        Swal.fire({
            title: 'ຢືນຢັນການລົບ?',
            text: "ຂໍ້ມູນລົດຄັນນີ້ຈະຖືກຍ້າຍໄປຖັງຂີ້ເຫຍື້ອ!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'ລົບເລີຍ',
            cancelButtonText: 'ຍົກເລີກ'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = 'actions/delete_vehicle.php?id=' + id;
            }
        });
    });

    // 3. Logic ສຳລັບປຸ່ມແກ້ໄຂ (Edit) - ສົ່ງຂໍ້ມູນໄປຫາ Modal
    $('.btn-edit').click(function() {
        const id = $(this).data('id');
        const plate = $(this).data('plate');
        const type = $(this).data('type');
        const seats = $(this).data('seats');
        const status = $(this).data('status');

        $('#edit_id').val(id);
        $('#edit_plate').val(plate);
        $('#edit_type').val(type);
        $('#edit_seats').val(seats);
        $('#edit_status').val(status);

        $('#editVehicleModal').modal('show');
    });
});
</script>