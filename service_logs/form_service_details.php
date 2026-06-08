<?php
session_start();
if (!isset($_SESSION['checked']) || $_SESSION['checked'] != 1) {
    echo "<script>alert('ກະລຸນາລ໋ອກອິນກ່ອນ');location='index.php';</script>";
    exit();
} else {
require_once("../cennect_dbstock.php");
if (!isset($connect)) { die("Error: ບໍ່ສາມາດເຊື່ອມຕໍ່ຖານຂໍ້ມູນ."); }
mysqli_set_charset($connect, "utf8");

$service_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($service_id <= 0) {
    echo "<script>alert('ລະບົບບໍ່ພົບ ID ບິນ'); window.location='select_service_logs.php';</script>";
    exit();
}

// 🛠️ API ສຳລັບ AJAX ປິດບິນ
if (isset($_GET['action']) && $_GET['action'] == 'update_status_print') {
    $sql_update = "UPDATE service_logs SET status = 'success', completed_at = NOW() WHERE log_id = $service_id";
    if (mysqli_query($connect, $sql_update)) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => mysqli_error($connect)]);
    }
    exit();
}

// 1. Logic ບັນທຶກຄ່າແຮງງານ
if (isset($_POST['btn_save_labor'])) {
    $labor = floatval($_POST['labor_cost']);
    $sql_labor = "UPDATE service_logs SET labor_cost = $labor WHERE log_id = $service_id";
    if (mysqli_query($connect, $sql_labor)) {
        header("Location: ?id=$service_id");
        exit();
    } else {
        echo "<script>alert('ຜິດພາດ: " . mysqli_real_escape_string($connect, mysqli_error($connect)) . "');</script>";
    }
}

// 🛠️ ເພີ່ມ Logic ສຳລັບຍົກເລີກ/ລ້າງຄ່າແຮງງານ (ປັບໃຫ້ເປັນ 0)
if (isset($_GET['action']) && $_GET['action'] == 'clear_labor') {
    $sql_clear = "UPDATE service_logs SET labor_cost = 0 WHERE log_id = $service_id";
    if (mysqli_query($connect, $sql_clear)) {
        header("Location: ?id=$service_id");
        exit();
    } else {
        echo "<script>alert('ຜິດພາດ: " . mysqli_real_escape_string($connect, mysqli_error($connect)) . "');</script>";
    }
}

// 2. Logic ບັນທຶກລາຍການອະໄຫຼ่ (ຖ້າມີການເລືອກຊ້ຳ ຈະລວມກັນທັນທີ)
if (isset($_POST['btn_save'])) {
    $part_val = intval($_POST['part_id']);
    $qty = intval($_POST['qty']);
    $price = floatval($_POST['price']);
    $description = mysqli_real_escape_string($connect, $_POST['description']);

    $can_save = true;
    if ($part_val > 0) {
        $check = mysqli_query($connect, "SELECT qty_stock FROM parts_profile WHERE part_id = $part_val");
        $row = mysqli_fetch_array($check);
        if (!$row || $row['qty_stock'] < $qty) {
            echo "<script>alert('ສະຕັອກບໍ່ພໍ! ໃນສະຕັອກເຫຼືອ: " . ($row['qty_stock'] ?? 0) . "');</script>";
            $can_save = false;
        }
    } else {
        echo "<script>alert('ກະລຸນາເລືອກອະໄຫຼ່ລົດກ່ອນບັນທຶກ!');</script>";
        $can_save = false;
    }

    if ($can_save) {
        $check_exist = mysqli_query($connect, "SELECT detail_id, qty FROM service_details WHERE service_id = $service_id AND part_id = $part_val");
        
        if (mysqli_num_rows($check_exist) > 0) {
            $exist_row = mysqli_fetch_array($check_exist);
            $new_qty = $exist_row['qty'] + $qty;
            $new_total = $new_qty * $price;
            $sql = "UPDATE service_details SET qty = $new_qty, total = $new_total WHERE detail_id = " . $exist_row['detail_id'];
        } else {
            $total = $qty * $price;
            $sql = "INSERT INTO service_details (service_id, part_id, description, qty, price, total) 
                    VALUES ($service_id, $part_val, '$description', $qty, $price, $total)";
        }

        if (mysqli_query($connect, $sql)) {
            mysqli_query($connect, "UPDATE parts_profile SET qty_stock = qty_stock - $qty WHERE part_id = $part_val");
            header("Location: ?id=$service_id");
            exit();
        } else {
            echo "<script>alert('Error: " . mysqli_real_escape_string($connect, mysqli_error($connect)) . "');</script>";
        }
    }
}

// 3. Logic ຍົກເລີກລາຍການອະໄຫຼ່ (ຄືນສະຕັອກ)
if (isset($_GET['action']) && $_GET['action'] == 'delete_item') {
    $del_id = intval($_GET['del_id']);
    
    $item_check = mysqli_query($connect, "SELECT part_id, qty FROM service_details WHERE detail_id = $del_id");
    if (mysqli_num_rows($item_check) > 0) {
        $item = mysqli_fetch_array($item_check);
        $p_id = $item['part_id'];
        $p_qty = $item['qty'];
        
        if ($p_id > 0) {
            mysqli_query($connect, "UPDATE parts_profile SET qty_stock = qty_stock + $p_qty WHERE part_id = $p_id");
        }
        mysqli_query($connect, "DELETE FROM service_details WHERE detail_id = $del_id");
    }
    header("Location: ?id=$service_id");
    exit();
}

// 4. ດຶງຂໍ້ມູນສະແດງຜົນ
$res_total = mysqli_query($connect, "SELECT SUM(total) as sum_parts FROM service_details WHERE service_id = $service_id");
$total_data = mysqli_fetch_array($res_total);
$sum_parts = $total_data['sum_parts'] ?? 0;

$res_log = mysqli_query($connect, "SELECT labor_cost, status FROM service_logs WHERE log_id = $service_id");
$log_data = mysqli_fetch_array($res_log);
$labor_cost = $log_data['labor_cost'] ?? 0;
$current_status = $log_data['status'] ?? 'pending';
$grand_total = $sum_parts + $labor_cost;

// 📦 ດຶງຂໍ້ມູນອະໄຫຼ່ທັງໝົດ
$parts_array = [];
$res_parts = mysqli_query($connect, "SELECT * FROM parts_profile");
while ($p = mysqli_fetch_array($res_parts)) {
    $barcode_key = (!empty($p['part_code'])) ? $p['part_code'] : $p['part_id'];
    
    $img_raw = trim($p['part_image']);
    if (!empty($img_raw)) {
        if (strpos($img_raw, 'uploads/') !== false) {
            $part_image_path = str_replace('uploads/', '../parts_profile/uploads/', $img_raw);
        } else {
            $part_image_path = '../parts_profile/uploads/' . $img_raw;
        }
    } else {
        $part_image_path = 'https://placehold.co/150x150?text=No+Image';
    }
    
    $parts_array[] = [
        'part_id'    => $p['part_id'],
        'barcode'    => strval($barcode_key),
        'part_name'  => $p['part_name'],
        'sale_price' => $p['sale_price'],
        'part_image' => $part_image_path,
        'qty_stock'  => intval($p['qty_stock'])
    ];
}
?>

<!DOCTYPE html>
<html lang="lo">
<head>
    <meta charset="UTF-8">
    <title>ຈັດການບິນ #<?php echo str_pad($service_id, 5, "0", STR_PAD_LEFT); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Noto+Sans+Lao:wght@400;500;700&display=swap');
        body { font-family: 'Noto Sans Lao', sans-serif; background-color: #f4f6f9; color: #333; }
        .card-custom { background: #fff; border: none; border-radius: 12px; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.04); }
        
        .table thead th { font-weight: 600; background-color: #f1f4f8; color: #495057; border-bottom: 2px solid #e9ecef; font-size: 12px; padding: 6px 10px; }
        .table tbody td { vertical-align: middle; color: #555; font-size: 13px; padding: 6px 10px; }
        .table tfoot td { font-size: 13px; padding: 6px 10px; }
        
        .btn { font-weight: 500; border-radius: 8px; }
        .summary-box { background: linear-gradient(135deg, #20c997 0%, #198754 100%); border-radius: 12px; padding: 15px; color: white; }
        .form-control, .form-select { border-radius: 8px; }
        
        .part-item-card { 
            cursor: pointer; 
            transition: all 0.2s ease-in-out; 
            border: 1px solid #e2e8f0; 
            border-radius: 10px; 
            overflow: hidden; 
            background: #fff; 
            display: flex;
            flex-direction: column;
            height: 100%;
        }
        .part-item-card:hover { transform: translateY(-3px); box-shadow: 0 4px 10px rgba(0,0,0,0.08); border-color: #3b82f6; }
        .part-item-card img { object-fit: contain !important; width: 100%; height: 100%; background-color: #fafafa; }

        @media print { body * { display: none !important; visibility: hidden !important; } #printFrame, #printFrame * { display: block !important; visibility: visible !important; width: 100% !important; height: 100% !important; } }
    </style>
</head>
<body>

<div class="container-fluid py-4 px-xl-5">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <div class="d-flex align-items-center mb-1">
                <a href="form_service_logs.php" class="btn btn-white btn-sm me-3 rounded-circle shadow-sm d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;" title="ກັບຄືນ">
                    <i class="fas fa-arrow-left text-secondary"></i>
                </a>
                <h3 class="fw-bold text-dark mb-0">ໜ້າຈັດການລາຍການສ້ອມແປງ (Dashboard)</h3>
            </div>
            <div class="d-flex align-items-center gap-3 ms-5">
                <p class="text-muted mb-0" style="font-size: 14px;">ເລກທີບິນ: <span class="fw-bold text-primary">#<?php echo str_pad($service_id, 5, "0", STR_PAD_LEFT); ?></span></p>
                <?php if($current_status == 'pending'): ?>
                    <span class="badge bg-warning text-dark rounded-pill px-3"><i class="fas fa-spinner fa-spin me-1"></i> ກຳລັງສ້ອມແປງ</span>
                <?php else: ?>
                    <span class="badge bg-success rounded-pill px-3"><i class="fas fa-check-circle me-1"></i> ສຳເລັດແລ້ວ</span>
                <?php endif; ?>
            </div>
        </div>

        <div>
            <button type="button" onclick="printInvoice(<?php echo $service_id; ?>)" class="btn btn-success d-flex align-items-center gap-2 px-4 py-2 shadow-sm fs-5 fw-bold">
                <i class="fas fa-print"></i> ພິມໃບບິນ & ປິດບິນ
            </button>
        </div>
    </div>

    <div class="row g-4">
        
        <div class="col-lg-8 col-xl-8">
            <div class="card-custom p-4 shadow-sm h-100">
                <h5 class="fw-bold text-primary mb-3"><i class="fas fa-boxes me-2"></i> ຄລີກເລືອກອະໄຫຼ່ລົດສ້ອມແປງ</h5>
                
                <form method="POST" action="?id=<?php echo $service_id; ?>" id="part_form">
                    <input type="hidden" name="btn_save" value="1">
                    <input type="hidden" name="part_id" id="part_id_hidden" required>
                    
                    <div class="mb-3">
                        <div class="input-group shadow-sm border rounded">
                            <span class="input-group-text bg-white text-muted border-0"><i class="fas fa-search"></i></span>
                            <input type="text" id="part_filter_input" class="form-control border-0 ps-0 py-2 fs-6" placeholder="ພިມຄົ້ນຫາຊື່ ຫຼື ຍິງບາໂຄດຢູ່ບ່ອນນີ້..." autocomplete="off">
                        </div>
                    </div>
                    
                    <div class="p-2 border rounded bg-light mb-3">
                        <div class="row row-cols-2 row-cols-sm-3 row-cols-md-4 row-cols-lg-5 g-2 overflow-y-auto" style="max-height: 520px; min-height: 350px;" id="parts_grid_display">
                        </div>
                    </div>
                    
                    <div class="p-3 bg-light rounded border mb-0 d-none">
                        <input type="text" name="description" id="description">
                        <input type="number" name="price" id="price">
                        <input type="number" name="qty" id="part_qty" value="1">
                    </div>
                </form>
            </div>
        </div>

        <div class="col-lg-4 col-xl-4">
            <div class="row mb-4 g-3">
                <div class="col-12">
                    <div class="card-custom p-3 h-100 d-flex align-items-center">
                        <form method="POST" action="?id=<?php echo $service_id; ?>" class="w-100" onsubmit="prepareLaborSubmit()">
                            <div class="row align-items-center g-2">
                                <div class="col-sm-3">
                                    <label class="form-label mb-0 fw-bold text-secondary" style="font-size:13px;"><i class="fas fa-tools text-warning me-1"></i> ຄ່າແຮງ</label>
                                </div>
                                <div class="col-sm-5">
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text bg-light border-end-0">₭</span>
                                        <input type="text" id="labor_cost_display" class="form-control border-start-0 ps-0 fw-bold text-end" value="<?php echo number_format($labor_cost); ?>" required placeholder="0">
                                        <input type="hidden" name="labor_cost" id="labor_cost_real" value="<?php echo $labor_cost; ?>">
                                    </div>
                                </div>
                                <div class="col-sm-4 d-flex gap-1">
                                    <button type="submit" name="btn_save_labor" class="btn btn-warning btn-sm w-50 fw-bold py-1" title="ບັນທຶກຄ່າແຮງ">
                                        <i class="fas fa-save"></i>
                                    </button>
                                    <?php if ($labor_cost > 0): ?>
                                        <a href="?id=<?php echo $service_id; ?>&action=clear_labor" class="btn btn-danger btn-sm w-50 py-1 d-flex align-items-center justify-content-center" onclick="return confirm('ຕ້ອງການຍົກເລີກ ຫຼື ລ້າງຄ່າແຮງງານໃຫ້ເປັນ 0 ແມ່ນບໍ່?')" title="ຍົກເລີກຄ່າແຮງ">
                                            <i class="fas fa-times"></i>
                                        </a>
                                    <?php else: ?>
                                        <button type="button" class="btn btn-secondary btn-sm w-50 py-1" disabled title="ບໍ່ມີຄ່າແຮງໃຫ້ຍົກເລີກ">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="col-12">
                    <div class="summary-box d-flex flex-column justify-content-center align-items-center text-center shadow-sm">
                        <span class="text-white-50 small text-uppercase mb-1">ຍອດລວມສຸດທິທັງໝົດ</span>
                        <h4 class="fw-bold mb-0 text-white"><?php echo number_format($grand_total); ?> ກີບ</h4>
                    </div>
                </div>
            </div>

            <div class="card-custom p-0 overflow-hidden shadow-sm">
                <div class="p-2 bg-white border-bottom">
                    <h6 class="mb-0 fw-bold text-dark" style="font-size:13px;"><i class="fas fa-file-invoice text-primary me-2"></i>ລາຍການອະໄຫຼ່ໃນບິນນີ້</h6>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th class="text-center" width="35">#</th>
                                <th>ລາຍການ</th>
                                <th class="text-center" width="60">ຈຳນວນ</th>
                                <th class="text-end" width="100">ລວມ (ກີບ)</th>
                                <th class="text-center" width="40"></th> </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $res_det = mysqli_query($connect, "SELECT d.*, p.part_name FROM service_details d LEFT JOIN parts_profile p ON d.part_id = p.part_id WHERE d.service_id = $service_id");
                            if (mysqli_num_rows($res_det) == 0) {
                                echo "<tr><td colspan='5' class='text-center text-muted py-4'><i class='fas fa-box-open fs-5 mb-2 d-block text-black-50'></i>ຍັງບໍ່ມີລາຍການອະໄຫຼ່</td></tr>";
                            } else {
                                $i = 1;
                                while($d = mysqli_fetch_array($res_det)) {
                                    echo "<tr>
                                            <td class='text-center text-muted'>".$i."</td>
                                            <td class='fw-medium text-truncate' style='max-width:120px;' title='".($d['part_name'] ?? $d['description'])."'>".($d['part_name'] ?? $d['description'])."</td>
                                            <td class='text-center'><span class='badge bg-light text-dark border px-2 py-1'>".$d['qty']."</span></td>
                                            <td class='text-end fw-bold text-dark'>".number_format($d['total'])."</td>
                                            <td class='text-center'>
                                                <a href='?id=".$service_id."&action=delete_item&del_id=".$d['detail_id']."' 
                                                   class='text-danger' 
                                                   onclick='return confirm(\"ຕ້ອງການຍົກເລີກລາຍການນີ້?\")' title='ຍົກເລີກ'>
                                                     <i class='fas fa-trash-alt'></i>
                                                </a>
                                            </td>
                                          </tr>";
                                    $i++;
                                }
                            }
                            ?>
                        </tbody>
                        <tfoot class="bg-light">
                            <tr>
                                <td colspan="3" class="text-end text-muted">ລວມຄ່າອາໄຫຼ່:</td>
                                <td class="text-end fw-bold text-secondary"><?php echo number_format($sum_parts); ?></td>
                                <td></td> </tr>
                            <tr>
                                <td colspan="3" class="text-end border-0 text-muted">ຄ່າແຮງງານ:</td>
                                <td class="text-end border-0 fw-bold text-warning">+<?php echo number_format($labor_cost); ?></td>
                                <td class="border-0"></td> </tr>
                            <tr class="table-primary" style="border-top: 2px solid #0d6efd;">
                                <td colspan="3" class="text-end fw-bold text-primary">ຍອດລວມທັງໝົດ:</td>
                                <td class="text-end fw-bold text-primary fs-6"><?php echo number_format($grand_total); ?></td>
                                <td></td> </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>

<iframe id="printFrame" style="display:none;"></iframe>

<script>
const partsStockList = <?php echo json_encode($parts_array); ?>;

$(document).ready(function() {
    renderPartsGrid(partsStockList);

    $('#labor_cost_display').on('input', function() {
        let val = $(this).val().replace(/[^0-9.]/g, '');
        if (val !== '') {
            let num = parseFloat(val);
            $(this).val(num.toLocaleString('en-US'));
            $('#labor_cost_real').val(val);
        } else {
            $(this).val('');
            $('#labor_cost_real').val(0);
        }
    });

    $('#part_filter_input').on('keypress', function(e) {
        if (e.which === 13) {
            e.preventDefault();
            var searchVal = $(this).val().trim().toLowerCase();
            var matched = partsStockList.find(function(item) {
                return item.barcode.toLowerCase() === searchVal || String(item.part_id) === searchVal;
            });
            if (matched) {
                autoSubmitPart(matched.part_id, matched.part_name, matched.sale_price);
                $(this).val('');
            } else {
                alert('ບໍ່ພົບລະຫັດອະໄຫຼ່ນີ້ໃນລະບົບ!');
                $(this).val('');
            }
        }
    });

    $('#part_filter_input').on('input', function() {
        var searchVal = $(this).val().trim().toLowerCase();
        var filtered = partsStockList.filter(function(item) {
            return item.part_name.toLowerCase().includes(searchVal) || 
                   item.barcode.toLowerCase().includes(searchVal);
        });
        renderPartsGrid(filtered);
    });

    $(document).on('click', '.part-item-card', function() {
        var id = $(this).data('id');
        var name = $(this).data('name');
        var price = $(this).data('price');
        autoSubmitPart(id, name, price);
    });
});

function prepareLaborSubmit() {
    let rawVal = $('#labor_cost_display').val().replace(/[^0-9.]/g, '');
    $('#labor_cost_real').val(rawVal === '' ? 0 : rawVal);
}

function autoSubmitPart(id, name, price) {
    $('#part_id_hidden').val(id);
    $('#description').val(name);
    $('#price').val(price);
    $('#part_qty').val('1'); 
    $('#part_form').submit();
}

function renderPartsGrid(items) {
    var grid = $('#parts_grid_display');
    grid.empty();
    
    if (items.length === 0) {
        grid.html('<div class="col-12 text-center text-muted py-4"><i class="fas fa-search-minus d-block fs-3 mb-2 text-black-50"></i>ບໍ່ພົບອະໄຫຼ່ທີ່ຄົ້ນຫາ</div>');
        return;
    }
    
    items.forEach(function(item) {
        var cardHtml = `
            <div class="col">
                <div class="card part-item-card p-2 text-center shadow-sm" 
                     data-id="${item.part_id}" 
                     data-name="${item.part_name}" 
                     data-price="${item.sale_price}">
                    <div class="ratio ratio-1x1 mb-2 bg-light rounded overflow-hidden">
                        <img src="${item.part_image}" alt="${item.part_name}" class="img-fluid" onerror="this.src='https://placehold.co/150x150?text=No+Image'">
                    </div>
                    <div class="small fw-bold text-dark text-truncate mb-2" style="font-size:12px; padding: 0 2px;" title="${item.part_name}">${item.part_name}</div>
                    
                    <div class="d-flex justify-content-between align-items-center mt-auto">
                        <span class="badge bg-primary-subtle text-primary px-2 py-1" style="font-size:11px; font-weight:600;">${Number(item.sale_price).toLocaleString()} ₭</span>
                        <span class="badge bg-danger-subtle text-danger px-2 py-1" style="font-size:11px; font-weight:600;">ສາງ: ${item.qty_stock}</span>
                    </div>
                </div>
            </div>
        `;
        grid.append(cardHtml);
    });
}

function printInvoice(serviceId) {
    $.ajax({
        url: window.location.pathname + '?id=' + serviceId + '&action=update_status_print',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if(response.status === 'success') {
                var iframe = document.getElementById('printFrame');
                iframe.src = 'print_service_logs.php?id=' + serviceId;
                iframe.onload = function() {
                    setTimeout(function() {
                        iframe.contentWindow.focus();
                        iframe.contentWindow.print();
                        window.location.reload();
                    }, 300); 
                };
            } else {
                alert('ເກີດຂໍ້ຜິດພາດໃນການປ່ຽນສະຖານະບິນ!');
            }
        },
        error: function() {
            var iframe = document.getElementById('printFrame');
            iframe.src = 'print_service_logs.php?id=' + serviceId;
            iframe.onload = function() {
                iframe.contentWindow.focus();
                iframe.contentWindow.print();
            };
        }
    });
}
</script>
</body>
</html>
<?php
}
?>