<?php
require_once '../../config/config.php';
require_once '../../config/database.php';
require_once '../../includes/utils.php';

$db = new Database();
$conn = $db->connect();

$errors = [];

// Get suppliers and purchase contracts for dropdown
$stmt = $conn->query("SELECT id, code, name FROM suppliers ORDER BY name");
$suppliers = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $conn->query("SELECT id, contract_number, title FROM purchase_contracts WHERE status = 'active' ORDER BY created_at DESC");
$contracts = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $supplier_id = intval($_POST['supplier_id']);
    $purchase_contract_id = !empty($_POST['purchase_contract_id']) ? intval($_POST['purchase_contract_id']) : null;
    $po_date = Utils::sanitize($_POST['po_date']);
    $title = Utils::sanitize($_POST['title']);
    $description = Utils::sanitize($_POST['description']);
    $total_amount = floatval($_POST['total_amount']);
    $currency = Utils::sanitize($_POST['currency']);
    $status = Utils::sanitize($_POST['status']);
    $delivery_date = Utils::sanitize($_POST['delivery_date']);
    $notes = Utils::sanitize($_POST['notes']);
    
    if (empty($supplier_id)) $errors[] = 'Vui lòng chọn nhà cung cấp';
    if (empty($po_date)) $errors[] = 'Ngày PO không được để trống';
    if (empty($title)) $errors[] = 'Tiêu đề PO không được để trống';
    
    if (empty($errors)) {
        try {
            $po_year = date('Y', strtotime($po_date));
            $po_number = Utils::generateContractNumber('purchase_order', $po_year);
            
            $sql = "INSERT INTO purchase_orders 
                    (po_number, purchase_contract_id, supplier_id, po_date, po_year, title, description, 
                     total_amount, currency, status, delivery_date, notes) 
                    VALUES 
                    (:po_number, :purchase_contract_id, :supplier_id, :po_date, :po_year, :title, :description, 
                     :total_amount, :currency, :status, :delivery_date, :notes)";
            
            $stmt = $conn->prepare($sql);
            $stmt->execute([
                ':po_number' => $po_number,
                ':purchase_contract_id' => $purchase_contract_id,
                ':supplier_id' => $supplier_id,
                ':po_date' => $po_date,
                ':po_year' => $po_year,
                ':title' => $title,
                ':description' => $description,
                ':total_amount' => $total_amount,
                ':currency' => $currency,
                ':status' => $status,
                ':delivery_date' => $delivery_date ?: null,
                ':notes' => $notes
            ]);
            
            header('Location: list.php?success=created');
            exit;
        } catch(PDOException $e) {
            $errors[] = 'Lỗi database: ' . $e->getMessage();
        }
    }
}

include '../../includes/header.php';
?>

<div class="row">
    <div class="col-12">
        <h2><i class="bi bi-plus-circle"></i> Tạo đơn đặt hàng mua (PO) mới</h2>
    </div>
</div>

<?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <ul class="mb-0">
            <?php foreach ($errors as $error): ?>
                <li><?php echo $error; ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<div class="card">
    <div class="card-body">
        <form method="POST" action="">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="supplier_id" class="form-label required">Nhà cung cấp</label>
                        <select class="form-select" id="supplier_id" name="supplier_id" required>
                            <option value="">-- Chọn nhà cung cấp --</option>
                            <?php foreach ($suppliers as $supplier): ?>
                                <option value="<?php echo $supplier['id']; ?>">
                                    <?php echo htmlspecialchars($supplier['code'] . ' - ' . $supplier['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="purchase_contract_id" class="form-label">Hợp đồng liên kết (tùy chọn)</label>
                        <select class="form-select" id="purchase_contract_id" name="purchase_contract_id">
                            <option value="">-- Không liên kết --</option>
                            <?php foreach ($contracts as $contract): ?>
                                <option value="<?php echo $contract['id']; ?>">
                                    <?php echo htmlspecialchars($contract['contract_number'] . ' - ' . $contract['title']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="po_date" class="form-label required">Ngày PO</label>
                        <input type="date" class="form-control" id="po_date" name="po_date" required value="<?php echo date('Y-m-d'); ?>">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="delivery_date" class="form-label">Ngày giao hàng dự kiến</label>
                        <input type="date" class="form-control" id="delivery_date" name="delivery_date">
                    </div>
                </div>
            </div>
            
            <div class="mb-3">
                <label for="title" class="form-label required">Tiêu đề</label>
                <input type="text" class="form-control" id="title" name="title" required>
            </div>
            
            <div class="mb-3">
                <label for="description" class="form-label">Mô tả</label>
                <textarea class="form-control" id="description" name="description" rows="3"></textarea>
            </div>
            
            <div class="row">
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="total_amount" class="form-label">Giá trị</label>
                        <input type="number" step="0.01" class="form-control" id="total_amount" name="total_amount" value="0">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="currency" class="form-label">Đơn vị tiền tệ</label>
                        <select class="form-select" id="currency" name="currency">
                            <option value="VND" selected>VND</option>
                            <option value="USD">USD</option>
                            <option value="EUR">EUR</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="status" class="form-label">Trạng thái</label>
                        <select class="form-select" id="status" name="status">
                            <option value="draft" selected>Nháp</option>
                            <option value="submitted">Đã gửi</option>
                            <option value="approved">Đã duyệt</option>
                            <option value="completed">Hoàn thành</option>
                            <option value="cancelled">Đã hủy</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <div class="mb-3">
                <label for="notes" class="form-label">Ghi chú</label>
                <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
            </div>
            
            <div class="alert alert-info">
                <i class="bi bi-info-circle"></i> Số PO sẽ được tạo tự động: PO-[Năm]-[Số thứ tự]
            </div>
            
            <div class="d-flex justify-content-between">
                <a href="list.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Quay lại</a>
                <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Tạo PO</button>
            </div>
        </form>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
