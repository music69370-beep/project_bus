<style>
    body { font-family: 'Noto Sans Lao', sans-serif; background-color: #f8f9fa; margin: 0; padding: 0; }
    
    .sidebar { 
        min-height: 100vh; 
        background: #2c3e50; 
        color: white;
        position: sticky; /* ໃຫ້ Sidebar ຢູ່ກັບບ່ອນເວລາ Scroll */
        top: 0;
    }
    
    /* ຕັດ Padding ຂອງ container-fluid ອອກ */
    .container-fluid { padding: 0 !important; }
    
    /* ສ່ວນ Main Content ໃຫ້ມີໄລຍະຫ່າງໜ້ອຍໜຶ່ງເພື່ອຄວາມສວຍງາມ */
    .main-content { 
        padding: 30px; 
        background-color: #f8f9fa;
        min-height: 100vh;
    }

    .sidebar a { color: #bdc3c7; text-decoration: none; padding: 15px 25px; display: block; transition: 0.3s; }
    .sidebar a:hover { background: #1a252f; color: white; padding-left: 30px; }
    .sidebar a.active { background: #1abc9c; color: white; border-left: 5px solid #16a085; }
</style>
<div class="col-md-2 sidebar">
    <div class="py-4 px-3">
        <h5 class="text-white mb-0"><i class="fas fa-bus-alt me-2 text-info"></i> ຄິວລົດອັດສະລິຍະ</h5>
    </div>
    
    <div class="mt-3">
        <a href="index.php" class="active"><i class="fas fa-tachometer-alt me-2"></i> Dashboard</a>
        
        <hr class="text-secondary mx-3 my-2"> <a href="manage_bookings.php"><i class="fas fa-ticket-alt me-2 text-warning"></i> ການຈອງປີ້</a>
        <a href="manage_trips.php"><i class="fas fa-calendar-check me-2"></i> ຈັດການຖ້ຽວລົດ</a>
        
        <hr class="text-secondary mx-3 my-2">
        
        <a href="manage_routes.php"><i class="fas fa-map-marked-alt me-2"></i> ຈັດການເສັ້ນທາງ</a>
        <a href="manage_schedules.php"><i class="fas fa-clock me-2"></i> ຈັດການເວລາອອກລົດ</a>
        <a href="manage_vehicles.php"><i class="fas fa-bus me-2"></i> ຂໍ້ມູນລົດ</a>
        <a href="manage_drivers.php"><i class="fas fa-user-tie me-2"></i> ຄົນຂັບລົດ</a>
        
        <hr class="text-secondary mx-3 my-2">
        
        <a href="reports.php"><i class="fas fa-chart-pie me-2"></i> ບົດລາຍງານ</a>
    </div>

    <div style="position: absolute; bottom: 20px; width: 100%;">
        <a href="logout.php" class="text-danger"><i class="fas fa-sign-out-alt me-2"></i> ອອກຈາກລະບົບ</a>
    </div>
</div>
<div class="col-md-10 main-content">