<?php
require_once '../../config/config.php';
require_once '../../config/database.php';
require_once '../../includes/utils.php';

$db = new Database();
$conn = $db->connect();

$errors = [];

// Get all customers for dropdown
$stmt = $conn->query("SELECT id, code, name FROM customers ORDER BY name");
$customers = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validate input
    $customer_id = intval($_POST['customer_id']);
    $contract_date = Utils::sanitize($_POST['contract_date']);
    $archive_number = Utils::sanitize($_POST['archive_number']);
    $title = Utils::sanitize($_POST['title']);
    $description = Utils::sanitize($_POST['description']);
    $total_amount = floatval($_POST['total_amount']);
    $cost_amount = floatval($_POST['cost_amount']);
    $currency = Utils::sanitize($_POST['currency']);
    $status = Utils::sanitize($_POST['status']);
    $start_date = Utils::sanitize($_POST['start_date']);
    $end_date = Utils::sanitize($_POST['end_date']);
    $payment_terms = Utils::sanitize($_POST['payment_terms']);
    $notes = Utils::sanitize($_POST['notes']);
    
    if (empty($customer_id)) $errors[] = 'Vui lòng chọn khách hàng';
    if (empty($contract_date)) $errors[] = 'Ngày hợp đồng không được để trống';
    if (empty($title)) $errors[] = 'Tiêu đề hợp đồng không được để trống';
    if ($total_amount <= 0) $errors[] = 'Giá trị hợp đồng phải lớn hơn 0';
    
    if (empty($errors)) {
        try {
            // Generate contract number
            $contract_year = date('Y', strtotime($contract_date));
            $contract_number = Utils::generateContractNumber('sales_contract', $contract_year, $archive_number);
            
            // Get customer name for folder creation
            $stmt = $conn->prepare("SELECT name FROM customers WHERE id = :id");
            $stmt->execute([':id' => $customer_id]);
            $customer = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Create folder for contract documents
            $folder_path = Utils::createContractFolder($contract_year, $customer['name'], $contract_number);
            
            // Insert contract
            $sql = "INSERT INTO sales_contracts 
                    (contract_number, customer_id, contract_date, contract_year, archive_number, title, description, 
                     total_amount, cost_amount, currency, status, start_date, end_date, payment_terms, notes, folder_path) 
                    VALUES 
                    (:contract_number, :customer_id, :contract_date, :contract_year, :archive_number, :title, :description, 
                     :total_amount, :cost_amount, :currency, :status, :start_date, :end_date, :payment_terms, :notes, :folder_path)";
            
            $stmt = $conn->prepare($sql);
            $stmt->execute([
                ':contract_number' => $contract_number,
                ':customer_id' => $customer_id,
                ':contract_date' => $contract_date,
                ':contract_year' => $contract_year,
                ':archive_number' => $archive_number,
                ':title' => $title,
                ':description' => $description,
                ':total_amount' => $total_amount,
                ':cost_amount' => $cost_amount,
                ':currency' => $currency,
                ':status' => $status,
                ':start_date' => $start_date ?: null,
                ':end_date' => $end_date ?: null,
                ':payment_terms' => $payment_terms,
                ':notes' => $notes,
                ':folder_path' => $folder_path
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
        <h2><i class="bi bi-plus-circle"></i> Tạo hợp đồng bán mới</h2>
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
                                <option value="<?php echo $customer['id']; ?>" <?php echo (isset($_POST['customer_id']) && $_POST['customer_id'] == $customer['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($customer['code'] . ' - ' . $customer['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="contract_date" class="form-label required">Ngày hợp đồng</label>
                        <input type="date" class="form-control" id="contract_date" name="contract_date" required value="<?php echo $_POST['contract_date'] ?? date('Y-m-d'); ?>">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="archive_number" class="form-label">Số tập hồ sơ</label>
                        <input type="text" class="form-control" id="archive_number" name="archive_number" placeholder="VD: A1, B2" value="<?php echo $_POST['archive_number'] ?? ''; ?>">
                    </div>
                </div>
            </div>
            
            <div class="mb-3">
                <label for="title" class="form-label required">Tiêu đề hợp đồng</label>
                <input type="text" class="form-control" id="title" name="title" required value="<?php echo $_POST['title'] ?? ''; ?>">
            </div>
            
            <div class="mb-3">
                <label for="description" class="form-label">Mô tả</label>
                <textarea class="form-control" id="description" name="description" rows="3"><?php echo $_POST['description'] ?? ''; ?></textarea>
            </div>
            
            <div class="row">
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="total_amount" class="form-label required">Giá trị hợp đồng</label>
                        <input type="number" step="0.01" class="form-control" id="total_amount" name="total_amount" required value="<?php echo $_POST['total_amount'] ?? '0'; ?>">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="cost_amount" class="form-label">Chi phí (giá vốn)</label>
                        <input type="number" step="0.01" class="form-control" id="cost_amount" name="cost_amount" value="<?php echo $_POST['cost_amount'] ?? '0'; ?>">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="currency" class="form-label">Đơn vị tiền tệ</label>
                        <select class="form-select" id="currency" name="currency">
                            <option value="VND" selected>VND</option>
                            <option value="USD">USD</option>
                            <option value="EUR">EUR</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="status" class="form-label">Trạng thái</label>
                        <select class="form-select" id="status" name="status">
                            <option value="draft" selected>Nháp</option>
                            <option value="active">Đang hiệu lực</option>
                            <option value="completed">Hoàn thành</option>
                            <option value="cancelled">Đã hủy</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="start_date" class="form-label">Ngày bắt đầu</label>
                        <input type="date" class="form-control" id="start_date" name="start_date" value="<?php echo $_POST['start_date'] ?? ''; ?>">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="end_date" class="form-label">Ngày kết thúc</label>
                        <input type="date" class="form-control" id="end_date" name="end_date" value="<?php echo $_POST['end_date'] ?? ''; ?>">
                    </div>
                </div>
            </div>
            
            <div class="mb-3">
                <label for="payment_terms" class="form-label">Điều khoản thanh toán</label>
                <textarea class="form-control" id="payment_terms" name="payment_terms" rows="3"><?php echo $_POST['payment_terms'] ?? ''; ?></textarea>
            </div>
            
            <div class="mb-3">
                <label for="notes" class="form-label">Ghi chú</label>
                <textarea class="form-control" id="notes" name="notes" rows="3"><?php echo $_POST['notes'] ?? ''; ?></textarea>
            </div>
            
            <div class="alert alert-info">
                <i class="bi bi-info-circle"></i> Số hợp đồng sẽ được tạo tự động theo quy tắc: SC-[Năm]-[Tập hồ sơ]-[Số thứ tự]
            </div>
            
            <div class="d-flex justify-content-between">
                <a href="list.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Quay lại</a>
                <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Tạo hợp đồng</button>
            </div>
        </form>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
