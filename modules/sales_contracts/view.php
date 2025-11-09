<?php
require_once '../../config/config.php';
require_once '../../config/database.php';
require_once '../../includes/utils.php';

$db = new Database();
$conn = $db->connect();

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    header('Location: list.php');
    exit;
}

// Get contract details
$sql = "SELECT sc.*, c.name as customer_name, c.contact_person, c.phone, c.email 
        FROM sales_contracts sc 
        LEFT JOIN customers c ON sc.customer_id = c.id 
        WHERE sc.id = :id";
$stmt = $conn->prepare($sql);
$stmt->execute([':id' => $id]);
$contract = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$contract) {
    header('Location: list.php');
    exit;
}

$profit = $contract['total_amount'] - $contract['cost_amount'];

include '../../includes/header.php';
?>

<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="bi bi-file-earmark-text"></i> Chi tiết hợp đồng bán</h2>
            <div>
                <a href="edit.php?id=<?php echo $id; ?>" class="btn btn-warning"><i class="bi bi-pencil"></i> Sửa</a>
                <a href="list.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Quay lại</a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">Thông tin hợp đồng</h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <th width="30%">Số hợp đồng:</th>
                        <td><strong><?php echo htmlspecialchars($contract['contract_number']); ?></strong></td>
                    </tr>
                    <tr>
                        <th>Tiêu đề:</th>
                        <td><?php echo htmlspecialchars($contract['title']); ?></td>
                    </tr>
                    <tr>
                        <th>Ngày hợp đồng:</th>
                        <td><?php echo Utils::formatDate($contract['contract_date']); ?></td>
                    </tr>
                    <tr>
                        <th>Năm hợp đồng:</th>
                        <td><?php echo $contract['contract_year']; ?></td>
                    </tr>
                    <?php if ($contract['archive_number']): ?>
                    <tr>
                        <th>Số tập hồ sơ:</th>
                        <td><?php echo htmlspecialchars($contract['archive_number']); ?></td>
                    </tr>
                    <?php endif; ?>
                    <tr>
                        <th>Trạng thái:</th>
                        <td><?php echo Utils::getStatusBadge($contract['status']); ?></td>
                    </tr>
                    <tr>
                        <th>Giá trị hợp đồng:</th>
                        <td><strong class="text-success"><?php echo Utils::formatCurrency($contract['total_amount'], $contract['currency']); ?></strong></td>
                    </tr>
                    <tr>
                        <th>Chi phí (giá vốn):</th>
                        <td><?php echo Utils::formatCurrency($contract['cost_amount'], $contract['currency']); ?></td>
                    </tr>
                    <tr>
                        <th>Lợi nhuận:</th>
                        <td><strong class="<?php echo $profit >= 0 ? 'text-success' : 'text-danger'; ?>">
                            <?php echo Utils::formatCurrency($profit, $contract['currency']); ?>
                            <?php if ($contract['total_amount'] > 0): ?>
                                (<?php echo number_format(($profit / $contract['total_amount']) * 100, 2); ?>%)
                            <?php endif; ?>
                        </strong></td>
                    </tr>
                    <?php if ($contract['start_date']): ?>
                    <tr>
                        <th>Ngày bắt đầu:</th>
                        <td><?php echo Utils::formatDate($contract['start_date']); ?></td>
                    </tr>
                    <?php endif; ?>
                    <?php if ($contract['end_date']): ?>
                    <tr>
                        <th>Ngày kết thúc:</th>
                        <td><?php echo Utils::formatDate($contract['end_date']); ?></td>
                    </tr>
                    <?php endif; ?>
                    <?php if ($contract['description']): ?>
                    <tr>
                        <th>Mô tả:</th>
                        <td><?php echo nl2br(htmlspecialchars($contract['description'])); ?></td>
                    </tr>
                    <?php endif; ?>
                    <?php if ($contract['payment_terms']): ?>
                    <tr>
                        <th>Điều khoản thanh toán:</th>
                        <td><?php echo nl2br(htmlspecialchars($contract['payment_terms'])); ?></td>
                    </tr>
                    <?php endif; ?>
                    <?php if ($contract['notes']): ?>
                    <tr>
                        <th>Ghi chú:</th>
                        <td><?php echo nl2br(htmlspecialchars($contract['notes'])); ?></td>
                    </tr>
                    <?php endif; ?>
                    <?php if ($contract['folder_path']): ?>
                    <tr>
                        <th>Thư mục tài liệu:</th>
                        <td><code><?php echo htmlspecialchars($contract['folder_path']); ?></code></td>
                    </tr>
                    <?php endif; ?>
                </table>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0">Thông tin khách hàng</h5>
            </div>
            <div class="card-body">
                <h6><?php echo htmlspecialchars($contract['customer_name']); ?></h6>
                <?php if ($contract['contact_person']): ?>
                    <p class="mb-1"><i class="bi bi-person"></i> <?php echo htmlspecialchars($contract['contact_person']); ?></p>
                <?php endif; ?>
                <?php if ($contract['phone']): ?>
                    <p class="mb-1"><i class="bi bi-telephone"></i> <?php echo htmlspecialchars($contract['phone']); ?></p>
                <?php endif; ?>
                <?php if ($contract['email']): ?>
                    <p class="mb-1"><i class="bi bi-envelope"></i> <?php echo htmlspecialchars($contract['email']); ?></p>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Thông tin khác</h5>
            </div>
            <div class="card-body">
                <p class="mb-1"><small class="text-muted">Ngày tạo:</small><br>
                <?php echo Utils::formatDate($contract['created_at'], 'd/m/Y H:i'); ?></p>
                <p class="mb-0"><small class="text-muted">Cập nhật lần cuối:</small><br>
                <?php echo Utils::formatDate($contract['updated_at'], 'd/m/Y H:i'); ?></p>
            </div>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
