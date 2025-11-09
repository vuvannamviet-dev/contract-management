<?php
require_once '../../config/config.php';
require_once '../../config/database.php';
require_once '../../includes/utils.php';

$db = new Database();
$conn = $db->connect();

// Get all sales contracts with customer information
$sql = "SELECT sc.*, c.name as customer_name 
        FROM sales_contracts sc 
        LEFT JOIN customers c ON sc.customer_id = c.id 
        ORDER BY sc.created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->execute();
$contracts = $stmt->fetchAll(PDO::FETCH_ASSOC);

include '../../includes/header.php';
?>

<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="bi bi-file-earmark-text"></i> Danh sách hợp đồng bán</h2>
            <a href="create.php" class="btn btn-primary"><i class="bi bi-plus-circle"></i> Tạo hợp đồng bán</a>
        </div>
    </div>
</div>

<?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?php 
        if ($_GET['success'] == 'created') echo 'Tạo hợp đồng bán thành công!';
        if ($_GET['success'] == 'updated') echo 'Cập nhật hợp đồng bán thành công!';
        if ($_GET['success'] == 'deleted') echo 'Xóa hợp đồng bán thành công!';
        ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Số HĐ</th>
                        <th>Ngày HĐ</th>
                        <th>Khách hàng</th>
                        <th>Tiêu đề</th>
                        <th>Giá trị</th>
                        <th>Chi phí</th>
                        <th>Lợi nhuận</th>
                        <th>Trạng thái</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($contracts)): ?>
                        <tr>
                            <td colspan="9" class="text-center">Chưa có hợp đồng bán nào</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($contracts as $contract): ?>
                            <?php $profit = $contract['total_amount'] - $contract['cost_amount']; ?>
                            <tr>
                                <td><?php echo htmlspecialchars($contract['contract_number']); ?></td>
                                <td><?php echo Utils::formatDate($contract['contract_date']); ?></td>
                                <td><?php echo htmlspecialchars($contract['customer_name']); ?></td>
                                <td><?php echo htmlspecialchars($contract['title']); ?></td>
                                <td><?php echo Utils::formatCurrency($contract['total_amount'], $contract['currency']); ?></td>
                                <td><?php echo Utils::formatCurrency($contract['cost_amount'], $contract['currency']); ?></td>
                                <td class="<?php echo $profit >= 0 ? 'text-success' : 'text-danger'; ?>">
                                    <?php echo Utils::formatCurrency($profit, $contract['currency']); ?>
                                </td>
                                <td><?php echo Utils::getStatusBadge($contract['status']); ?></td>
                                <td class="action-buttons">
                                    <a href="view.php?id=<?php echo $contract['id']; ?>" class="btn btn-sm btn-info" title="Xem">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="edit.php?id=<?php echo $contract['id']; ?>" class="btn btn-sm btn-warning" title="Sửa">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <a href="delete.php?id=<?php echo $contract['id']; ?>" class="btn btn-sm btn-danger btn-delete" title="Xóa">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
