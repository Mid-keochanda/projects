<?php
session_start();

// ກວດສອບ Login ດ້ວຍ SweetAlert2
if (!isset($_SESSION['checked']) || $_SESSION['checked'] != 1) {
    echo "<!DOCTYPE html><html><head><script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script></head><body><script>
    Swal.fire({icon: 'error', title: 'ແຈ້ງເຕືອນ', text: 'ກະລຸນາລ໋ອກອິນກ່ອນ', confirmButtonText: 'ຕົກລົງ'}).then(() => { location='index.php'; });
    </script></body></html>";
    exit();
}

require_once("../cennect_dbstock.php");
if (!isset($connect)) { die("Error: ບໍ່ສາມາດເຊື່ອມຕໍ່ຖານຂໍ້ມູນ."); }
mysqli_set_charset($connect, "utf8");

// ສ້າງກະຕ່າຊົ່ວຄາວ ຖ້າຍັງບໍ່ມີ
if (!isset($_SESSION['pos_cart'])) {
    $_SESSION['pos_cart'] = [];
}

// 🛠️ API ສຳລັບ AJAX ບັນທຶກບິນຕົວຈິງ ແລະ ພິມບິນ (ປ່ຽນມາຮັບແບບ POST ເພື່ອຮັບຄ່າການຈ່າຍເງິນ)
if (isset($_POST['action']) && $_POST['action'] == 'process_sale') {
    if (empty($_SESSION['pos_cart'])) {
        echo json_encode(['status' => 'error', 'message' => 'ບໍ່ມີສິນຄ້າໃນກະຕ່າ! ກະລຸນາເລືອກສິນຄ້າກ່ອນ.']);
        exit();
    }

    $pay_status = $_POST['payment_status'] ?? 'Paid';
    $pay_type = $_POST['payment_type'] ?? 'ເງິນສົດ';
    
    // ຄຳນວນຍອດລວມອີກຮອບເພື່ອຄວາມຊົວ
    $total_amount = 0;
    foreach ($_SESSION['pos_cart'] as $item) {
        $total_amount += $item['total'];
    }

    // 1. ສ້າງບິນຂາຍໃໝ່ (ກຳນົດເປັນ success ເລີຍ)
    $sql_insert_log = "INSERT INTO service_logs (status, created_at, completed_at) VALUES ('success', NOW(), NOW())";
    if (mysqli_query($connect, $sql_insert_log)) {
        $new_log_id = mysqli_insert_id($connect);

        // 2. ບັນທຶກລາຍການສິນຄ້າ
        foreach ($_SESSION['pos_cart'] as $item) {
            $p_id = $item['part_id'];
            $qty = $item['qty'];
            $price = $item['price'];
            $total = $item['total'];
            $desc = mysqli_real_escape_string($connect, $item['name']);

            $sql_det = "INSERT INTO service_details (log_id, part_id, description, qty, price, total) 
                        VALUES ($new_log_id, $p_id, '$desc', $qty, $price, $total)";
            mysqli_query($connect, $sql_det);
        }

        // 3. ບັນທຶກລົງຕາຕະລາງ Invoices
        $inv_no = "INV" . date('Ymd') . str_pad($new_log_id, 4, "0", STR_PAD_LEFT);
        $stmt_inv = $connect->prepare("INSERT INTO invoices (inv_no, log_id, inv_date, total_amount, net_amount, payment_status, payment_type) VALUES (?, ?, CURDATE(), ?, ?, ?, ?)");
        $stmt_inv->bind_param("siddds", $inv_no, $new_log_id, $total_amount, $total_amount, $pay_status, $pay_type);
        $stmt_inv->execute();
        $stmt_inv->close();

        // 4. ລ້າງກະຕ່າ Session ຖິ້ມ
        unset($_SESSION['pos_cart']);

        // ສົ່ງ ID ບິນທີ່ສ້າງສຳເລັດແລ້ວກັບໄປໃຫ້ JavaScript ເພື່ອພິມ
        echo json_encode(['status' => 'success', 'log_id' => $new_log_id]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'ບໍ່ສາມາດສ້າງບິນໄດ້: ' . mysqli_error($connect)]);
    }
    exit();
}

// 1. Logic ເພີ່ມເຂົ້າກະຕ່າ ແລະ ຕັດສະຕັອກທັນທີ!
if (isset($_POST['btn_save'])) {
    $part_val = intval($_POST['part_id']);
    $qty = intval($_POST['qty']);
    $price = floatval($_POST['price']);
    $description = mysqli_real_escape_string($connect, $_POST['description']);

    if ($part_val > 0) {
        $check = mysqli_query($connect, "SELECT qty_stock FROM parts_profile WHERE part_id = $part_val");
        $row = mysqli_fetch_array($check);
        
        if ($row && $row['qty_stock'] >= $qty) {
            
            // ຕັດສະຕັອກໃນຖານຂໍ້ມູນທັນທີ
            mysqli_query($connect, "UPDATE parts_profile SET qty_stock = qty_stock - $qty WHERE part_id = $part_val");

            // ເພີ່ມເຂົ້າ Session (ຖ້າມີແລ້ວໃຫ້ບວກຈຳນວນ)
            $found = false;
            foreach ($_SESSION['pos_cart'] as &$item) {
                if ($item['part_id'] == $part_val) {
                    $item['qty'] += $qty;
                    $item['total'] = $item['qty'] * $price;
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                $_SESSION['pos_cart'][] = [
                    'cart_id' => uniqid(),
                    'part_id' => $part_val,
                    'name'    => $description,
                    'qty'     => $qty,
                    'price'   => $price,
                    'total'   => $qty * $price
                ];
            }
        } else {
            // ເກັບ Error ໄວ້ໃນ Session ເພື່ອໄປສະແດງ SweetAlert ໜ້າ UI
            $_SESSION['swal_error'] = "ເຄື່ອງໃນສາງບໍ່ພໍ! ສະຕັອກເຫຼືອຕົວຈິງ: " . ($row['qty_stock'] ?? 0);
        }
    }
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// 2. Logic ລຶບລາຍການອອກຈາກກະຕ່າ ແລະ ຄືນສະຕັອກທັນທີ!
if (isset($_GET['action']) && $_GET['action'] == 'delete_item') {
    $cart_id = $_GET['cart_id'];
    foreach ($_SESSION['pos_cart'] as $key => $item) {
        if ($item['cart_id'] == $cart_id) {
            
            // ຄືນສະຕັອກໃຫ້ສາງ
            $return_qty = $item['qty'];
            $return_id = $item['part_id'];
            mysqli_query($connect, "UPDATE parts_profile SET qty_stock = qty_stock + $return_qty WHERE part_id = $return_id");
            
            // ລຶບອອກຈາກກະຕ່າ Session
            unset($_SESSION['pos_cart'][$key]);
            break;
        }
    }
    $_SESSION['pos_cart'] = array_values($_SESSION['pos_cart']); // ຈັດລຽງ Array ໃໝ່
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// 3. ຄິດໄລ່ຍອດລວມຈາກກະຕ່າ Session
$grand_total = 0;
if (!empty($_SESSION['pos_cart'])) {
    foreach ($_SESSION['pos_cart'] as $item) {
        $grand_total += $item['total'];
    }
}

// ດຶງຂໍ້ມູນລາຍການອະໄຫຼ່ທັງໝົດໃນສາງ
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
        'sale_price' => floatval($p['sale_price']),
        'part_image' => $part_image_path,
        'qty_stock'  => intval($p['qty_stock'])
    ];
}
?>

<!DOCTYPE html>
<html lang="lo">
<head>
    <meta charset="UTF-8">
    <title>ໜ້າຂາຍອະໄຫຼ່ (POS)</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Noto+Sans+Lao:wght@400;500;700&display=swap');
        body { font-family: 'Noto Sans Lao', sans-serif; background-color: #f4f6f9; color: #333; }
        .card-custom { background: #fff; border: none; border-radius: 12px; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.04); }
        .table thead th { font-weight: 600; background-color: #f1f4f8; color: #495057; border-bottom: 2px solid #e9ecef; font-size: 12px; padding: 6px 10px; }
        .table tbody td { vertical-align: middle; color: #555; font-size: 13px; padding: 6px 10px; }
        .table tfoot td { font-size: 13px; padding: 6px 10px; }
        .summary-box { background: linear-gradient(135deg, #20c997 0%, #198754 100%); border-radius: 12px; padding: 15px; color: white; }
        .part-item-card { cursor: pointer; transition: all 0.2s ease-in-out; border: 1px solid #e2e8f0; border-radius: 10px; overflow: hidden; background: #fff; display: flex; flex-direction: column; height: 100%; }
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
                <a href="sale_history.php" class="btn btn-white btn-sm me-3 rounded-circle shadow-sm d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;" title="ກັບຄືນ">
                    <i class="fas fa-arrow-left text-secondary"></i>
                </a>
                <h3 class="fw-bold text-dark mb-0">ລະບົບຂາຍອະໄຫຼ່ໜ້າຮ້ານ</h3>
            </div>
            <div class="d-flex align-items-center gap-3 ms-5">
                <span class="badge bg-warning text-dark rounded-pill px-3"><i class="fas fa-shopping-cart me-1"></i> ກຳລັງເລືອກສິນຄ້າ (ຍັງບໍ່ບັນທຶກບິນ)</span>
            </div>
        </div>

        <div>
            <button type="button" onclick="openPaymentModal()" class="btn btn-primary d-flex align-items-center gap-2 px-4 py-2 shadow-sm fs-5 fw-bold" <?php if(empty($_SESSION['pos_cart'])) echo 'disabled'; ?>>
                <i class="fas fa-money-bill-wave"></i> <span id="checkoutText">ຮັບເງິນ & ພິມໃບບິນຂາຍ</span>
            </button>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8 col-xl-8">
            <div class="card-custom p-4 shadow-sm h-100">
                <h5 class="fw-bold text-primary mb-3"><i class="fas fa-boxes me-2"></i> ເລືອກລາຍການອະໄຫຼ່ທີ່ຕ້ອງການຂາຍ</h5>
                
                <form method="POST" action="" id="part_form">
                    <input type="hidden" name="btn_save" value="1">
                    <input type="hidden" name="part_id" id="part_id_hidden" required>
                    
                    <div class="mb-3">
                        <div class="input-group shadow-sm border rounded">
                            <span class="input-group-text bg-white text-muted border-0"><i class="fas fa-search"></i></span>
                            <input type="text" id="part_filter_input" class="form-control border-0 ps-0 py-2 fs-6" placeholder="ຍິງບາໂຄດ ຫຼື ພິມຄົ້ນຫາຊື່ສິນຄ້າຢູ່ບ່ອນນີ້..." autocomplete="off" autofocus>
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
                    <div class="summary-box d-flex flex-column justify-content-center align-items-center text-center shadow-sm py-4">
                        <span class="text-white-50 small text-uppercase mb-1">ຍອດລວມທີ່ຕ້ອງຊຳລະທັງໝົດ</span>
                        <h3 class="fw-bold mb-0 text-white"><?php echo number_format($grand_total); ?> ກີບ</h3>
                    </div>
                </div>
            </div>

            <div class="card-custom p-0 overflow-hidden shadow-sm">
                <div class="p-2 bg-white border-bottom">
                    <h6 class="mb-0 fw-bold text-dark" style="font-size:13px;"><i class="fas fa-shopping-basket text-primary me-2"></i>ລາຍການສິນຄ້າໃນກະຕ່າ</h6>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th class="text-center" width="35">#</th>
                                <th>ລາຍການສິນຄ້າ</th>
                                <th class="text-center" width="60">ຈຳນວນ</th>
                                <th class="text-end" width="100">ລວມ (ກີບ)</th>
                                <th class="text-center" width="40"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            if (empty($_SESSION['pos_cart'])) {
                                echo "<tr><td colspan='5' class='text-center text-muted py-4'><i class='fas fa-box-open fs-5 mb-2 d-block text-black-50'></i>ຍັງບໍ່ມີລາຍການສິນຄ້າໃນກະຕ່າ</td></tr>";
                            } else {
                                $i = 1;
                                foreach($_SESSION['pos_cart'] as $item) {
                                    echo "<tr>
                                            <td class='text-center text-muted'>".$i."</td>
                                            <td class='fw-medium text-truncate' style='max-width:120px;' title='".$item['name']."'>".$item['name']."</td>
                                            <td class='text-center'><span class='badge bg-light text-dark border px-2 py-1'>".$item['qty']."</span></td>
                                            <td class='text-end fw-bold text-dark'>".number_format($item['total'])."</td>
                                            <td class='text-center'>
                                                <a href='javascript:void(0)' class='text-danger' onclick='confirmDeleteItem(\"".$item['cart_id']."\")' title='ລຶບອອກ'>
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
                            <tr class="table-primary" style="border-top: 2px solid #0d6efd;">
                                <td colspan="3" class="text-end fw-bold text-primary">ຍອດລວມສຸດທິ:</td>
                                <td class="text-end fw-bold text-primary fs-6"><?php echo number_format($grand_total); ?></td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="paymentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow" style="border-radius: 15px; border: none;">
            <div class="modal-header text-white" style="background: linear-gradient(135deg, #0d6efd 0%, #0dcaf0 100%); border-radius: 15px 15px 0 0;">
                <h5 class="modal-title fw-bold"><i class="fas fa-cash-register me-2"></i> ຢືນຢັນການຂາຍ ແລະ ຊຳລະເງິນ</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 text-center">
                <p class="text-muted mb-1">ຍອດລວມທີ່ຕ້ອງຊຳລະ:</p>
                <h3 class="text-danger fw-bold mb-4"><?php echo number_format($grand_total); ?> ກີບ</h3>
                
                <div class="mb-3 text-start">
                    <label class="form-label fw-bold"><i class="fas fa-wallet text-success me-1"></i> ວິທີຊຳລະເງິນ</label>
                    <select id="pay_type_select" class="form-select form-select-lg" style="border-radius: 10px;">
                        <option value="ເງິນສົດ">💵 ເງິນສົດ (Cash)</option>
                        <option value="ເງິນໂອນ">📱 ເງິນໂອນ (Transfer)</option>
                    </select>
                </div>

                <div id="cash_calc_block" class="mb-4 text-start">
                    <div class="p-3 bg-light rounded border border-warning" style="border-radius:10px;">
                        <label class="form-label fw-bold"><i class="fas fa-hand-holding-usd text-warning me-1"></i> ຮັບເງິນມາ (ກີບ)</label>
                        <div class="input-group mb-2">
                            <input type="text" id="received_amount_display" class="form-control form-control-lg text-end fw-bold text-success" placeholder="ປ້ອນຈຳນວນເງິນທີ່ຮັບມາ...">
                            <input type="hidden" id="received_amount_real" value="0">
                        </div>
                        <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                            <span class="fw-bold text-secondary">ເງິນທອນ:</span>
                            <span id="change_amount_display" class="fw-bold fs-5 text-danger">0 ກີບ</span>
                        </div>
                    </div>
                </div>

                <div id="qr_code_block" class="mb-4 text-center" style="display: none;">
                    <div class="p-3 bg-light rounded border border-primary" style="border-radius:10px;">
                        <p class="fw-bold text-primary mb-2"><i class="fas fa-qrcode me-1"></i> ສະແກນ QR Code ເພື່ອໂອນເງິນ</p>
                        <?php 
                            // ສ້າງ QR Code ຕາມຍອດລວມ (ບໍ່ມີເລກບິນ ເພາະຍັງບໍ່ບັນທຶກ)
                            $bank_name = "BCEL"; 
                            $account_name = "MID KEOCHANDA"; 
                            $account_number = "141122531890"; 
                            $qr_url = "https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=" . urlencode("BANK:$bank_name|ACC:$account_number|NAME:$account_name|AMOUNT:$grand_total|BILL:POS");
                        ?>
                        <img src="<?php echo $qr_url; ?>" alt="QR Code ຮັບເງິນ" class="img-fluid border rounded p-2 bg-white shadow-sm" style="max-width: 200px;">
                        <p class="small text-muted mt-2 mb-0 fw-bold">
                            ຊື່ບັນຊີ: <span class="text-dark fs-6"><?php echo $account_name; ?></span> <br>
                            ເລກບັນຊີ: <span class="text-dark font-monospace fs-6"><?php echo $account_number; ?></span>
                        </p>
                    </div>
                </div>
                
                <button type="button" id="btn_confirm_print" onclick="confirmAndPrintSale()" class="btn btn-primary w-100 py-3 fw-bold fs-5 shadow-sm mt-2" style="border-radius: 10px;">
                    <i class="fas fa-check-circle me-2"></i> ຢືນຢັນ & ພິມບິນ
                </button>
            </div>
        </div>
    </div>
</div>

<iframe id="printFrame" style="display:none;"></iframe>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<?php if(isset($_SESSION['swal_error'])): ?>
<script>
    Swal.fire({
        icon: 'error',
        title: 'ແຈ້ງເຕືອນ',
        text: '<?php echo $_SESSION['swal_error']; ?>',
        confirmButtonText: 'ຕົກລົງ'
    });
</script>
<?php unset($_SESSION['swal_error']); endif; ?>

<script>
const partsStockList = <?php echo json_encode($parts_array); ?>;
const grandTotal = <?php echo $grand_total; ?>;

$(document).ready(function() {
    renderPartsGrid(partsStockList);

    var savedWindowScroll = sessionStorage.getItem('scroll_window');
    var savedGridScroll = sessionStorage.getItem('scroll_grid');

    if (savedWindowScroll !== null) {
        $(window).scrollTop(savedWindowScroll);
        sessionStorage.removeItem('scroll_window'); 
    }
    if (savedGridScroll !== null) {
        $('#parts_grid_display').scrollTop(savedGridScroll);
        sessionStorage.removeItem('scroll_grid'); 
    }

    $('#part_filter_input').on('keypress', function(e) {
        if (e.which === 13) {
            e.preventDefault();
            var searchVal = $(this).val().trim().toLowerCase();
            var matched = partsStockList.find(function(item) {
                return item.barcode.toLowerCase() === searchVal || String(item.part_id) === searchVal;
            });
            if (matched) {
                saveScrollPosition(); 
                autoSubmitPart(matched.part_id, matched.part_name, matched.sale_price);
                $(this).val('');
            } else {
                Swal.fire({
                    icon: 'warning',
                    title: 'ບໍ່ພົບຂໍ້ມູນ',
                    text: 'ບໍ່ພົບລະຫັດອະໄຫຼ່ນີ້ໃນລະບົບສາງ!',
                    timer: 2000,
                    showConfirmButton: false
                });
                $(this).val('');
            }
        }
    });

    $('#part_filter_input').on('input', function() {
        var searchVal = $(this).val().trim().toLowerCase();
        var filtered = partsStockList.filter(function(item) {
            return item.part_name.toLowerCase().includes(searchVal) || item.barcode.toLowerCase().includes(searchVal);
        });
        renderPartsGrid(filtered);
    });

    $(document).on('click', '.part-item-card', function() {
        saveScrollPosition(); 
        var id = $(this).data('id');
        var name = $(this).data('name');
        var price = $(this).data('price');
        autoSubmitPart(id, name, price);
    });

    // -----------------------------------------------------
    // Logic: ສະຫຼັບ ບ໋ອກເງິນສົດ / QR Code ແລະ ຄຳນວນເງິນ
    // -----------------------------------------------------
    $('#pay_type_select').on('change', function() {
        let val = $(this).val();
        if (val === 'ເງິນສົດ') {
            $('#cash_calc_block').slideDown();
            $('#qr_code_block').slideUp();
            checkCashPayment(); 
        } else {
            $('#cash_calc_block').slideUp();
            $('#qr_code_block').slideDown();
            $('#btn_confirm_print').prop('disabled', false); // ໂອນເງິນ ກົດປິດບິນໄດ້ເລີຍ
        }
    });

    // ເມື່ອພິມຈຳນວນເງິນຮັບມາ
    $('#received_amount_display').on('input', function() {
        let val = $(this).val().replace(/[^0-9]/g, ''); 
        if (val !== '') {
            let num = parseInt(val);
            $(this).val(num.toLocaleString('en-US'));
            $('#received_amount_real').val(num);
        } else {
            $(this).val('');
            $('#received_amount_real').val(0);
        }
        checkCashPayment();
    });

    // ເອີ້ນເຮັດວຽກຄັ້ງທຳອິດຕອນໂຫຼດ
    setTimeout(function() {
        $('#pay_type_select').trigger('change');
    }, 200);
});

// ກວດສອບ ແລະ ຄຳນວນເງິນທອນ
function checkCashPayment() {
    if ($('#pay_type_select').val() === 'ເງິນສົດ') {
        let received = parseFloat($('#received_amount_real').val()) || 0;
        let change = received - grandTotal;
        
        if (change >= 0) {
            $('#change_amount_display').text(change.toLocaleString('en-US') + ' ກີບ').removeClass('text-danger').addClass('text-success');
            $('#btn_confirm_print').prop('disabled', false); 
        } else {
            $('#change_amount_display').text('ຍັງຂາດ ' + Math.abs(change).toLocaleString('en-US') + ' ກີບ').removeClass('text-success').addClass('text-danger');
            $('#btn_confirm_print').prop('disabled', true); 
        }
    } else {
        $('#btn_confirm_print').prop('disabled', false);
    }
}

// ເປີດ Modal ຖ້າມີສິນຄ້າໃນກະຕ່າ
function openPaymentModal() {
    if (grandTotal <= 0) {
        Swal.fire({icon: 'warning', title: 'ກະຕ່າວ່າງເປົ່າ', text: 'ກະລຸນາເລືອກສິນຄ້າກ່ອນຊຳລະເງິນ'});
        return;
    }
    $('#paymentModal').modal('show');
}

function confirmDeleteItem(cartId) {
    saveScrollPosition(); 
    Swal.fire({
        title: 'ຢືນຢັນການລຶບ?',
        text: "ທ່ານຕ້ອງການລຶບລາຍການນີ້ອອກຈາກກະຕ່າ ແລະ ຄືນສະຕັອກແທ້ບໍ?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'ລຶບອອກ',
        cancelButtonText: 'ຍົກເລີກ'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = '?action=delete_item&cart_id=' + cartId;
        }
    });
}

function saveScrollPosition() {
    sessionStorage.setItem('scroll_window', $(window).scrollTop());
    sessionStorage.setItem('scroll_grid', $('#parts_grid_display').scrollTop());
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
        grid.html('<div class="col-12 text-center text-muted py-4"><i class="fas fa-search-minus d-block fs-3 mb-2 text-black-50"></i>ບໍ່ພົບອະໄຫຼ່ສິນຄ້າທີ່ຄົ້ນຫາ</div>');
        return;
    }
    items.forEach(function(item) {
        var cardHtml = `
            <div class="col">
                <div class="card part-item-card p-2 text-center shadow-sm" data-id="${item.part_id}" data-name="${item.part_name}" data-price="${item.sale_price}">
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

// ຟັງຊັນບັນທຶກລົງ Database ແລະ ພິມບິນຜ່ານ Modal
function confirmAndPrintSale() {
    let payStatus = $('#pay_status_select').val();
    let payType = $('#pay_type_select').val();
    let btn = $('#btn_confirm_print');
    let originalHtml = btn.html();
    
    btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin ms-1"></i> ກຳລັງປະມວນຜົນ...');

    $.ajax({
        url: window.location.href.split('?')[0], // ຍິງເຂົ້າໄຟລ໌ຕົວມັນເອງ
        type: 'POST',
        dataType: 'json',
        data: {
            action: 'process_sale',
            payment_status: payStatus,
            payment_type: payType
        },
        success: function(response) {
            if(response.status === 'success') {
                $('#paymentModal').modal('hide');
                var iframe = document.getElementById('printFrame');
                
                let printUrl = 'print_service_logs.php?id=' + response.log_id;
                if (payType === 'ເງິນສົດ') {
                    let received = $('#received_amount_real').val();
                    let change = received - grandTotal;
                    printUrl += '&received=' + received + '&change=' + change;
                }

                iframe.src = printUrl;
                iframe.onload = function() {
                    setTimeout(function() {
                        iframe.contentWindow.focus();
                        iframe.contentWindow.print();
                        window.location.href = 'form_service_logs.php'; // ພິມແລ້ວກັບໄປໜ້າປະຫວັດທັນທີ
                    }, 300); 
                };
            } else {
                Swal.fire({icon: 'error', title: 'ຜິດພາດ', text: response.message});
                btn.prop('disabled', false).html(originalHtml);
            }
        },
        error: function() {
            Swal.fire({icon: 'error', title: 'ຜິດພາດ', text: 'ເກີດຂໍ້ຜິດພາດໃນການເຊື່ອມຕໍ່ລະບົບ!'});
            btn.prop('disabled', false).html(originalHtml);
        }
    });
}
</script>
</body>
</html>