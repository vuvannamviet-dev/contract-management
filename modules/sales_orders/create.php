<?php
require_once '../../config/config.php';
require_once '../../config/database.php';
require_once '../../includes/utils.php';

$db = new Database();
$conn = $db->connect();

$errors = [];

// Get customers and sales contracts for dropdown
$stmt = $conn->query("SELECT id, code, name FROM customers ORDER BY name");
$customers = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $conn->query("SELECT id, contract_number, title FROM sales_contracts WHERE status = 'active' ORDER BY created_at DESC");
$contracts = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $customer_id = intval($_POST['customer_id']);
    $sales_contract_id = !empty($_POST['sales_contract_id']) ? intval($_POST['sales_contract_id']) : null;
    $so_date = Utils::sanitize($_POST['so_date']);
    $title = Utils::sanitize($_POST['title']);
    $description = Utils::sanitize($_POST['description']);
    $total_amount = floatval($_POST['total_amount']);
    $currency = Utils::sanitize($_POST['currency']);
    $status = Utils::sanitize($_POST['status']);
    $delivery_date = Utils::sanitize($_POST['delivery_date']);
    $notes = Utils::sanitize($_POST['notes']);
    
    if (empty($customer_id)) $errors[] = 'Vui lòng chọn khách hàng';
    if (empty($so_date)) $errors[] = 'Ngày SO không được để trống';
    if (empty($title)) $errors[] = 'Tiêu đề SO không được để trống';
    
    if (empty($errors)) {
        try {
            $so_year = date('Y', strtotime($so_date));
            $so_number = Utils::generateContractNumber('sales_order', $so_year);
            
            $sql = "INSERT INTO sales_orders 
                    (so_number, sales_contract_id, customer_id, so_date, so_year, title, description, 
                     total_amount, currency, status, delivery_date, notes) 
                    VALUES 
                    (:so_number, :sales_contract_id, :customer_id, :so_date, :so_year, :title, :description, 
                     :total_amount, :currency, :status, :delivery_date, :notes)";
            
            $stmt = $conn->prepare($sql);
            $stmt->execute([
                ':so_number' => $so_number,
                ':sales_contract_id' => $sales_contract_id,
                ':customer_id' => $customer_id,
                ':so_date' => $so_date,
                ':so_year' => $so_year,
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
        <h2><i class="bi bi-plus-circle"></i> Tạo đơn đặt hàng bán (SO) mới</h2>
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
                        <label for="customer_id" class="form-label required">Khách hàng</label>
                        <select class="form-select" id="customer_id" name="customer_id" required>
                            <option value="">-- Chọn khách hàng --</option>
                            <?php foreach ($customers as $customer): ?>
                                <option value="<?php echo $customer['id']; ?>">
                                    <?php echo htmlspecialchars($customer['code'] . ' - ' . $customer['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="sales_contract_id" class="form-label">Hợp đồng liên kết (tùy chọn)</label>
                        <select class="form-select" id="sales_contract_id" name="sales_contract_id">
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
                        <label for="so_date" class="form-label required">Ngày SO</label>
                        <input type="date" class="form-control" id="so_date" name="so_date" required value="<?php echo date('Y-m-d'); ?>">
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
                <i class="bi bi-info-circle"></i> Số SO sẽ được tạo tự động: SO-[Năm]-[Số thứ tự]
            </div>
            
            <div class="d-flex justify-content-between">
                <a href="list.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Quay lại</a>
                <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Tạo SO</button>
            </div>
        </form>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
