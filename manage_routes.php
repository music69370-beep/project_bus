<?php 
include 'config/db.php';
include 'includes/header.php';
include 'includes/sidebar.php';

$stmt = $conn->prepare("SELECT * FROM Routes ORDER BY route_id DESC");
$stmt->execute();
$routes = $stmt->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><i class="fas fa-map-marked-alt text-success me-2"></i> ຈັດການເສັ້ນທາງ</h1>
    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addRouteModal">
        <i class="fas fa-plus me-2"></i> ເພີ່ມເສັ້ນທາງໃໝ່
    </button>
</div>

<div class="card card-custom bg-white p-4 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover">
            <thead class="table-light">
                <tr>
                    <th>ລຳດັບ</th>
                    <th>ຕົ້ນທາງ - ປາຍທາງ</th>
                    <th>ໄລຍະທາງ (km)</th>
                    <th>ລາຄາ/ຄົນ (ກີບ)</th>
                    <th>ລາຄາຝາກເຄື່ອງ (ກີບ)</th>
                    <th>ຈັດການ</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($routes as $index => $row): ?>
                <tr>
                    <td><?php echo $index + 1; ?></td>
                    <td><strong><?php echo $row['origin']; ?></strong> <i class="fas fa-arrow-right mx-2 text-muted"></i> <strong><?php echo $row['destination']; ?></strong></td>
                    <td><?php echo number_format($row['distance_km']); ?> km</td>
                    <td class="text-success font-weight-bold"><?php echo number_format($row['base_price_person']); ?></td>
                    <td><?php echo number_format($row['base_price_parcel']); ?></td>
                    <td>
                        <button class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i></button>
                        <button class="btn btn-sm btn-outline-danger btn-delete" data-id="<?php echo $row['route_id']; ?>"><i class="fas fa-trash"></i></button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="addRouteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="actions/save_route.php" method="POST">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">ເພີ່ມເສັ້ນທາງໃໝ່</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">ຕົ້ນທາງ</label>
                            <input type="text" name="origin" class="form-control" placeholder="ຕົວຢ່າງ: ວຽງຈັນ" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">ປາຍທາງ</label>
                            <input type="text" name="destination" class="form-control" placeholder="ຕົວຢ່າງ: ຫຼວງພະບາງ" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">ໄລຍະທາງ (km)</label>
                        <input type="number" name="distance_km" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">ລາຄາປີ້/ຄົນ (ກີບ)</label>
                        <input type="number" name="base_price_person" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">ລາຄາຝາກເຄື່ອງ (ກີບ)</label>
                        <input type="number" name="base_price_parcel" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">ບັນທຶກເສັ້ນທາງ</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>