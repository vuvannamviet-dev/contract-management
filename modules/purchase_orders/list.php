<?php
require_once '../../config/config.php';
require_once '../../config/database.php';
require_once '../../includes/utils.php';

$db = new Database();
$conn = $db->connect();

// Get all purchase orders with supplier information
$sql = "SELECT po.*, s.name as supplier_name, pc.contract_number 
        FROM purchase_orders po 
        LEFT JOIN suppliers s ON po.supplier_id = s.id 
        LEFT JOIN purchase_contracts pc ON po.purchase_contract_id = pc.id
        ORDER BY po.created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->execute();
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

include '../../includes/header.php';
?>

<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="bi bi-cart-dash"></i> Danh sách đơn đặt hàng mua (PO)</h2>
            <a href="create.php" class="btn btn-primary"><i class="bi bi-plus-circle"></i> Tạo PO</a>
        </div>
    </div>
</div>

<?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?php 
        if ($_GET['success'] == 'created') echo 'Tạo PO thành công!';
        if ($_GET['success'] == 'updated') echo 'Cập nhật PO thành công!';
        if ($_GET['success'] == 'deleted') echo 'Xóa PO thành công!';
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
                        <th>Số PO</th>
                        <th>Ngày PO</th>
                        <th>Nhà cung cấp</th>
                        <th>Hợp đồng</th>
                        <th>Tiêu đề</th>
                        <th>Giá trị</th>
                        <th>Trạng thái</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($orders)): ?>
                        <tr>
                            <td colspan="8" class="text-center">Chưa có đơn đặt hàng mua nào</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($orders as $order): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($order['po_number']); ?></td>
                                <td><?php echo Utils::formatDate($order['po_date']); ?></td>
                                <td><?php echo htmlspecialchars($order['supplier_name']); ?></td>
                                <td><?php echo htmlspecialchars($order['contract_number'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($order['title']); ?></td>
                                <td><?php echo Utils::formatCurrency($order['total_amount'], $order['currency']); ?></td>
                                <td><?php echo Utils::getStatusBadge($order['status']); ?></td>
                                <td class="action-buttons">
                                    <a href="view.php?id=<?php echo $order['id']; ?>" class="btn btn-sm btn-info" title="Xem">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="edit.php?id=<?php echo $order['id']; ?>" class="btn btn-sm btn-warning" title="Sửa">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <a href="delete.php?id=<?php echo $order['id']; ?>" class="btn btn-sm btn-danger btn-delete" title="Xóa">
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
