<?php 
include("../cennect_dbstock.php"); 
mysqli_set_charset($connect, "utf8");
?>
<!DOCTYPE html>
<html lang="lo">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ຈັດການການນຳເຂົ້າອາໄຫຼ່</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Noto+Sans+Lao:wght@400;500;700&display=swap');
        body { font-family: 'Noto Sans Lao', sans-serif; background-color: #f1f5f9; }
        .custom-card { border: none; border-radius: 15px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); }
        .table th { background-color: #4361ee !important; color: white !important; font-weight: 600; }
        .swal2-popup { font-family: 'Noto Sans Lao', sans-serif !important; border-radius: 15px !important; }
        .form-control-custom { border-radius: 10px; padding: 0.6rem 1rem; }
    </style>
</head>
<body>

<div class="container py-5">
    
    <div class="row mb-4 align-items-center g-3">
        <div class="col-md-5">
            <h3 class="fw-bold text-dark m-0"><i class="fas fa-file-download text-primary me-2"></i>ປະຫວັດການນຳເຂົ້າອາໄຫຼ່</h3>
            <p class="text-muted small mb-0"> ບັນທຶກ ແລະ ຕິດຕາມລາຍການຈັດຊື້ອາໄຫຼ່ເຂົ້າຄັງສະຕັອກ</p>
        </div>
        <div class="col-md-7 text-end d-flex gap-2 justify-content-md-end justify-content-between">
            <div class="input-group" style="max-width: 300px;">
                <span class="input-group-text bg-white border-0 shadow-sm" style="border-radius: 10px 0 0 10px;"><i class="fas fa-search text-muted"></i></span>
                <input type="text" id="searchPurchase" class="form-control border-0 shadow-sm" placeholder="ຄົ້ນຫາ: ລະຫັດ, ຊື່ອາໄຫຼ່, ບໍລິສັດ..." style="border-radius: 0 10px 10px 0;">
            </div>

            <button class="btn btn-primary fw-bold shadow-sm" style="border-radius: 10px;" data-bs-toggle="modal" data-bs-target="#addPurchaseModal">
                <i class="fas fa-plus-circle me-1"></i> ນຳເຂົ້າອາໄຫຼ່
            </button>
        </div>
    </div>

    <div class="card custom-card p-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="purchaseTable">
                <thead class="text-center">
                    <tr>
                        <th width="60">#</th>
                        <th>ວັນທີນຳເຂົ້າ</th>
                        <th>ລະຫັດອາໄຫຼ່</th>
                        <th class="text-start">ຊື່ອາໄຫຼ່</th>
                        <th>ຈຳນວນນຳເຂົ້າ</th>
                        <th>ລາຄາຊື້ (ຕໍ່ຊິ້ນ)</th>
                        <th>ມູນລະຄ່າລວມ</th>
                        <th class="text-start">ນຳເຂົ້າຈາກບໍລິສັດ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $i = 1;
                    $sql = "SELECT pur.purchase_date, pur.qty_bought, pur.buyer_price, pur.supplier_name, p.part_code, p.part_name 
                            FROM part_purchases pur
                            LEFT JOIN parts_profile p ON pur.part_id = p.part_id
                            ORDER BY pur.purchase_id DESC";
                    $result = mysqli_query($connect, $sql);
                    
                    if(mysqli_num_rows($result) == 0) {
                        echo "<tr><td colspan='8' class='text-center text-muted py-4'>--- ຍັງບໍ່ມີປະຫວັດການນຳເຂົ້າອາໄຫຼ່ ---</td></tr>";
                    }

                    while($row = mysqli_fetch_array($result)) {
                        $total_cost = $row['qty_bought'] * $row['buyer_price'];
                    ?>
                    <tr class="text-center search-row">
                        <td><?= $i++ ?></td>
                        <td><?= isset($row['purchase_date']) ? date('d/m/Y H:i', strtotime($row['purchase_date'])) : '-' ?></td>
                        <td><span class="badge bg-secondary part-code"><?= $row['part_code'] ?></span></td>
                        <td class="text-start fw-bold text-dark part-name"><?= $row['part_name'] ?></td>
                        <td class="fw-bold text-success">+ <?= number_format($row['qty_bought']) ?></td>
                        <td class="text-end"><?= number_format($row['buyer_price']) ?> ກີບ</td>
                        <td class="text-end fw-bold text-primary"><?= number_format($total_cost) ?> ກີບ</td>
                        <td class="text-start text-muted supplier-name"><?= $row['supplier_name'] ? $row['supplier_name'] : '-' ?></td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="addPurchaseModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 15px;">
            <div class="modal-header border-0 pt-4 px-4">
                <h5 class="fw-bold text-dark"><i class="fas fa-file-invoice-dollar text-primary me-2"></i>ຟອມນຳເຂົ້າອາໄຫຼ່</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="insert_part_purchases.php" method="POST">
                <div class="modal-body p-4 pt-0">
                    
                    <div class="mb-3">
                        <label class="form-label small fw-bold">ລະຫັດອາໄຫຼ່ / ບາໂຄດ</label>
                        <input type="text" id="barcode_input" class="form-control form-control-custom" placeholder="ພິມ ຫຼື ຍິງບາໂຄດອາໄຫຼ່..." autocomplete="off" required>
                        <input type="hidden" name="part_id" id="part_id_hidden">
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">ຊື່ອາໄຫຼ່</label>
                        <input type="text" id="part_name_display" class="form-control form-control-custom bg-light" placeholder="ຊື່ອາໄຫຼ່ຈະຂຶ້ນອັດຕະໂນມັດ" readonly>
                    </div>

                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label small fw-bold">ຈຳນວນທີ່ນຳເຂົ້າ</label>
                            <input type="number" name="qty_bought" id="qty_purchase" class="form-control form-control-custom" min="1" value="1" required>
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label small fw-bold text-muted">ລາຄາຊື້ຕໍ່ຊິ້ນ (ກີບ)</label>
                            <input type="number" name="buyer_price" id="purchase_price" class="form-control form-control-custom bg-light" min="0" readonly required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-primary">ລວມມູນຄ່າທັງໝົດ</label>
                        <input type="text" id="total_price_display" class="form-control form-control-custom bg-light fw-bold text-primary fs-5" value="0 ກີບ" readonly>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">ນຳເຂົ້າຈາກບໍລິສັດ</label>
                        <input type="text" name="supplier_name" class="form-control form-control-custom" placeholder="ເຊັ່ນ: ບໍລິສັດ ອາໄຫຼ່ລາວ ຈຳກັດ">
                    </div>

                </div>
                <div class="modal-footer border-0 pb-4 px-4">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal" style="border-radius: 10px;">ຍົກເລີກ</button>
                    <button type="submit" class="btn btn-primary px-4" style="border-radius: 10px;">ບັນທຶກ</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
$(document).ready(function() {
    // 🌟 ລະບົບຄົ້ນຫາປະຫວັດການນຳເຂົ້າ Real-time ຝັ່ງ Client
    $("#searchPurchase").on("keyup", function() {
        var value = $(this).val().toLowerCase();
        $("#purchaseTable tbody tr.search-row").filter(function() {
            var code = $(this).find('.part-code').text().toLowerCase();
            var name = $(this).find('.part-name').text().toLowerCase();
            var supplier = $(this).find('.supplier-name').text().toLowerCase();
            
            // ສະແດງແຖວທີ່ກົງກັບຄຳຄົ້ນຫາ (ລະຫັດ, ຊື່ອາໄຫຼ່, ຫຼື ບໍລິສັດ)
            $(this).toggle(code.indexOf(value) > -1 || name.indexOf(value) > -1 || supplier.indexOf(value) > -1);
        });
    });
});

const partsData = [
    <?php 
    $parts_query = mysqli_query($connect, "SELECT part_id, part_code, part_name, cost_price FROM parts_profile");
    while($p = mysqli_fetch_array($parts_query)) {
        echo "{ id: '{$p['part_id']}', code: '{$p['part_code']}', name: '" . addslashes($p['part_name']) . "', cost: {$p['cost_price']} },";
    }
    ?>
];

const barcodeInput = document.getElementById('barcode_input');
const partIdHidden = document.getElementById('part_id_hidden');
const partNameDisplay = document.getElementById('part_name_display');
const purchasePriceInput = document.getElementById('purchase_price');
const qtyPurchaseInput = document.getElementById('qty_purchase');
const totalPriceDisplay = document.getElementById('total_price_display');

function calculateTotal() {
    const qty = parseInt(qtyPurchaseInput.value) || 0;
    const price = parseFloat(purchasePriceInput.value) || 0;
    const total = qty * price;
    totalPriceDisplay.value = total.toLocaleString() + " ກີບ";
}

barcodeInput.addEventListener('input', function() {
    const inputValue = this.value.trim();
    const matchedPart = partsData.find(part => part.code === inputValue);
    
    if (matchedPart) {
        partIdHidden.value = matchedPart.id;
        partNameDisplay.value = matchedPart.name;
        purchasePriceInput.value = matchedPart.cost;
        barcodeInput.classList.remove('is-invalid');
        barcodeInput.classList.add('is-valid');
    } else {
        partIdHidden.value = "";
        partNameDisplay.value = "";
        purchasePriceInput.value = "";
        barcodeInput.classList.remove('is-valid');
    }
    calculateTotal();
});

qtyPurchaseInput.addEventListener('input', calculateTotal);

document.getElementById('addPurchaseModal').addEventListener('shown.bs.modal', function () {
    barcodeInput.focus();
});
</script>

</body>
</html>