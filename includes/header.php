<!DOCTYPE html>
<html lang="lo">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ລະບົບບໍລິຫານຄິວລົດ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Lao:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
    body { font-family: 'Noto Sans Lao', sans-serif; background-color: #f0f2f5; margin: 0; padding: 0; }
    
    /* ສີພື້ນ Sidebar */
    .sidebar { 
        min-height: 100vh; 
        background: #1e293b; /* ສີເນວີເຂັ້ມຕັດກັບຂຽວຈະເບິ່ງແພງ */
        color: white;
        position: sticky;
        top: 0;
    }
    
    .container-fluid { padding: 0 !important; }
    
    .main-content { 
        padding: 30px; 
        min-height: 100vh;
    }

    /* ເມນູປົກກະຕິ */
    .sidebar a { 
        color: #94a3b8; 
        text-decoration: none; 
        padding: 12px 20px; 
        display: block; 
        transition: 0.3s;
        font-size: 0.95rem;
    }

    /* ເມນູເວລາ Hover */
    .sidebar a:hover { 
        background: rgba(16, 185, 129, 0.1); 
        color: #10b981; /* ສີຂຽວອ່ອນ Emerald */
        padding-left: 25px;
    }

    /* ເມນູທີ່ກຳລັງເລືອກ (Active) */
    .sidebar a.active { 
        background: #10b981; /* ສີຂຽວອ່ອນຫຼັກ */
        color: white !important; 
        border-radius: 0 25px 25px 0; /* ເຮັດໃຫ້ຂອບມົນດ້ານຂວາ */
        margin-right: 15px;
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
    }

    /* ປັບສີ Card ໃນ Dashboard */
    .card-custom { 
        border-radius: 12px; 
        border: none; 
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        transition: 0.3s;
    }
    .card-custom:hover { transform: translateY(-5px); }
    
    .text-success-custom { color: #10b981 !important; }
</style>
</head>
<body>
<div class="container-fluid">
    <div class="row">

<div class="container-fluid p-0">
    <div class="row g-0">