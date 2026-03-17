<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<?php
// 1. ຕັ້ງຄ່າ Timezone
date_default_timezone_set('Asia/Vientiane');

$labels = [];
$revenue_data = [];

// 2. ດຶງຂໍ້ມູນຍ້ອນຫຼັງ 7 ວັນ
for ($i = 6; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $display_date = date('d/m', strtotime($date));
    
    // ດຶງຂໍ້ມູນ ແລະ ບັງຄັບໃຫ້ເປັນ 0 ຖ້າມັນວ່າງ (NULL)
    $stmt = $conn->prepare("SELECT IFNULL(SUM(net_amount), 0) as total FROM Bookings WHERE DATE(created_at) = ?");
    $stmt->execute([$date]);
    $row = $stmt->fetch();
    
    $labels[] = $display_date;
    $revenue_data[] = (float)$row['total'];
}

// 3. ດຶງຂໍ້ມູນສະຖານະລົດ (Pie Chart)
$v_stmt = $conn->query("SELECT 
    SUM(CASE WHEN status = 'Available' THEN 1 ELSE 0 END) as available,
    SUM(CASE WHEN status = 'Busy' THEN 1 ELSE 0 END) as busy,
    SUM(CASE WHEN status = 'Maintenance' THEN 1 ELSE 0 END) as maintenance
    FROM Vehicles WHERE is_deleted = 0");
$v_data = $v_stmt->fetch();
$vehicle_counts = [(int)$v_data['available'], (int)$v_data['busy'], (int)$v_data['maintenance']];

// ສົ່ງຂໍ້ມູນ PHP ໄປຫາ JavaScript ແບບ JSON
$js_labels = json_encode($labels);
$js_revenue = json_encode($revenue_data);
$js_vehicles = json_encode($vehicle_counts);
?>

<script>
$(document).ready(function() {
    // --- ກຣາບລາຍຮັບ (Line Chart) ---
    const ctxRev = document.getElementById('revenueChart');
    if (ctxRev) {
        new Chart(ctxRev.getContext('2d'), {
            type: 'line',
            data: {
                labels: <?php echo $js_labels; ?>,
                datasets: [{
                    label: 'ລາຍຮັບ (ກີບ)',
                    data: <?php echo $js_revenue; ?>,
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    borderWidth: 3,
                    tension: 0.4,
                    fill: true,
                    pointRadius: 5,
                    pointBackgroundColor: '#10b981',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false, // ໃຫ້ມັນຂະຫຍາຍຕາມ Div ທີ່ເຮົາກຳນົດຄວາມສູງໄວ້
                scales: {
                    y: { 
                        beginAtZero: true,
                        // ບັງຄັບໃຫ້ຂອບເຂດຕົວເລກແກນ Y ສູງສຸດຢູ່ທີ່ 1,000,000 ກີບ (ຫຼື ປັບຕາມໃຈ)
                        // ຖ້າມີຂໍ້ມູນອັນດຽວ ມັນກໍ່ຈະບໍ່ຍືດຈົນເຕັມຈໍ
                        suggestedMax: 1000000, 
                        ticks: {
                    stepSize: 200000, // ໃຫ້ມັນຂຶ້ນເທື່ອລະ 2 ແສນ
                    callback: function(value) { return value.toLocaleString() + ' ກີບ'; }
                }
            },
            x: {
                // ຄຸມແກນ X ໃຫ້ມີໄລຍະຫ່າງທີ່ພໍດີ
                grid: { display: false }
            }
        },
        plugins: {
            legend: { display: false }
        }
    }
        });
    }

    // --- ກຣາບສະຖານະລົດ (Doughnut Chart) ---
    const ctxStatus = document.getElementById('statusChart');
    if (ctxStatus) {
        new Chart(ctxStatus.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: ['ວ່າງ', 'ບໍ່ວ່າງ', 'ສ້ອມແປງ'],
                datasets: [{
                    data: <?php echo $js_vehicles; ?>,
                    backgroundColor: ['#10b981', '#3b82f6', '#f59e0b'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });
    }
});
</script>
<script>
function confirmDelete(id, type) {
    Swal.fire({
        title: 'ຢືນຢັນການລຶບ?',
        text: "ເຈົ້າຕ້ອງການລຶບຂໍ້ມູນນີ້ແທ້ບໍ່? ຂໍ້ມູນຈະຫາຍຖາວອນ!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'ໂດຍ, ລຶບເລີຍ!',
        cancelButtonText: 'ຍົກເລີກ'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = `actions/delete_${type}.php?id=${id}`;
        }
    });
}

// ລະບົບແຈ້ງເຕືອນອັດຕະໂນມັດ
const params = new URLSearchParams(window.location.search);
if (params.has('msg')) {
    const m = params.get('msg');
    if (m === 'success') Swal.fire('ສຳເລັດ!', 'ບັນທຶກຂໍ້ມູນຮຽບຮ້ອຍ', 'success');
    if (m === 'deleted') Swal.fire('ລຶບແລ້ວ!', 'ຂໍ້ມູນຖືກລຶບອອກຈາກລະບົບ', 'success');
    if (m === 'error') Swal.fire('ຜິດພາດ!', 'ເກີດຂໍ້ຜິດພາດບາງຢ່າງ', 'error');
}
</script>