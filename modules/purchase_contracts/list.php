<?php
require_once '../../config/config.php';
require_once '../../config/database.php';
require_once '../../includes/utils.php';

$db = new Database();
$conn = $db->connect();

// Get all purchase contracts with supplier information
$sql = "SELECT pc.*, s.name as supplier_name 
        FROM purchase_contracts pc 
        LEFT JOIN suppliers s ON pc.supplier_id = s.id 
        ORDER BY pc.created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->execute();
$contracts = $stmt->fetchAll(PDO::FETCH_ASSOC);

include '../../includes/header.php';
?>

<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="bi bi-file-earmark-text"></i> Danh sách hợp đồng mua</h2>
            <a href="create.php" class="btn btn-primary"><i class="bi bi-plus-circle"></i> Tạo hợp đồng mua</a>
        </div>
    </div>
</div>

<?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?php 
        if ($_GET['success'] == 'created') echo 'Tạo hợp đồng mua thành công!';
        if ($_GET['success'] == 'updated') echo 'Cập nhật hợp đồng mua thành công!';
        if ($_GET['success'] == 'deleted') echo 'Xóa hợp đồng mua thành công!';
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
                        <th>Nhà cung cấp</th>
                        <th>Tiêu đề</th>
                        <th>Giá trị</th>
                        <th>Trạng thái</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($contracts)): ?>
                        <tr>
                            <td colspan="7" class="text-center">Chưa có hợp đồng mua nào</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($contracts as $contract): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($contract['contract_number']); ?></td>
                                <td><?php echo Utils::formatDate($contract['contract_date']); ?></td>
                                <td><?php echo htmlspecialchars($contract['supplier_name']); ?></td>
                                <td><?php echo htmlspecialchars($contract['title']); ?></td>
                                <td><?php echo Utils::formatCurrency($contract['total_amount'], $contract['currency']); ?></td>
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
