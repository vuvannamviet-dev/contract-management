<?php
require_once '../../config/config.php';
require_once '../../config/database.php';
require_once '../../includes/utils.php';

$db = new Database();
$conn = $db->connect();

$errors = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validate input
    $code = Utils::sanitize($_POST['code']);
    $name = Utils::sanitize($_POST['name']);
    $contact_person = Utils::sanitize($_POST['contact_person']);
    $phone = Utils::sanitize($_POST['phone']);
    $email = Utils::sanitize($_POST['email']);
    $address = Utils::sanitize($_POST['address']);
    $tax_code = Utils::sanitize($_POST['tax_code']);
    $bank_account = Utils::sanitize($_POST['bank_account']);
    $bank_name = Utils::sanitize($_POST['bank_name']);
    $notes = Utils::sanitize($_POST['notes']);
    
    if (empty($code)) $errors[] = 'Mã khách hàng không được để trống';
    if (empty($name)) $errors[] = 'Tên khách hàng không được để trống';
    
    // Check if code already exists
    if (empty($errors)) {
        $stmt = $conn->prepare("SELECT id FROM customers WHERE code = :code");
        $stmt->execute([':code' => $code]);
        if ($stmt->fetch()) {
            $errors[] = 'Mã khách hàng đã tồn tại';
        }
    }
    
    if (empty($errors)) {
        try {
            $sql = "INSERT INTO customers (code, name, contact_person, phone, email, address, tax_code, bank_account, bank_name, notes) 
                    VALUES (:code, :name, :contact_person, :phone, :email, :address, :tax_code, :bank_account, :bank_name, :notes)";
            $stmt = $conn->prepare($sql);
            $stmt->execute([
                ':code' => $code,
                ':name' => $name,
                ':contact_person' => $contact_person,
                ':phone' => $phone,
                ':email' => $email,
                ':address' => $address,
                ':tax_code' => $tax_code,
                ':bank_account' => $bank_account,
                ':bank_name' => $bank_name,
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
        <h2><i class="bi bi-plus-circle"></i> Thêm khách hàng mới</h2>
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
                        <label for="code" class="form-label required">Mã khách hàng</label>
                        <input type="text" class="form-control" id="code" name="code" required value="<?php echo $_POST['code'] ?? ''; ?>">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="name" class="form-label required">Tên khách hàng</label>
                        <input type="text" class="form-control" id="name" name="name" required value="<?php echo $_POST['name'] ?? ''; ?>">
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="contact_person" class="form-label">Người liên hệ</label>
                        <input type="text" class="form-control" id="contact_person" name="contact_person" value="<?php echo $_POST['contact_person'] ?? ''; ?>">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="phone" class="form-label">Điện thoại</label>
                        <input type="text" class="form-control" id="phone" name="phone" value="<?php echo $_POST['phone'] ?? ''; ?>">
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?php echo $_POST['email'] ?? ''; ?>">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="tax_code" class="form-label">Mã số thuế</label>
                        <input type="text" class="form-control" id="tax_code" name="tax_code" value="<?php echo $_POST['tax_code'] ?? ''; ?>">
                    </div>
                </div>
            </div>
            
            <div class="mb-3">
                <label for="address" class="form-label">Địa chỉ</label>
                <textarea class="form-control" id="address" name="address" rows="2"><?php echo $_POST['address'] ?? ''; ?></textarea>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="bank_account" class="form-label">Số tài khoản</label>
                        <input type="text" class="form-control" id="bank_account" name="bank_account" value="<?php echo $_POST['bank_account'] ?? ''; ?>">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="bank_name" class="form-label">Ngân hàng</label>
                        <input type="text" class="form-control" id="bank_name" name="bank_name" value="<?php echo $_POST['bank_name'] ?? ''; ?>">
                    </div>
                </div>
            </div>
            
            <div class="mb-3">
                <label for="notes" class="form-label">Ghi chú</label>
                <textarea class="form-control" id="notes" name="notes" rows="3"><?php echo $_POST['notes'] ?? ''; ?></textarea>
            </div>
            
            <div class="d-flex justify-content-between">
                <a href="list.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Quay lại</a>
                <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Lưu</button>
            </div>
        </form>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
