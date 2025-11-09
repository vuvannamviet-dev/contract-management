<?php
require_once '../../config/config.php';
require_once '../../config/database.php';
require_once '../../includes/utils.php';

$db = new Database();
$conn = $db->connect();

// Get all sales orders with customer information
$sql = "SELECT so.*, c.name as customer_name, sc.contract_number 
        FROM sales_orders so 
        LEFT JOIN customers c ON so.customer_id = c.id 
        LEFT JOIN sales_contracts sc ON so.sales_contract_id = sc.id
        ORDER BY so.created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->execute();
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

include '../../includes/header.php';
?>

<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="bi bi-cart-plus"></i> Danh sách đơn đặt hàng bán (SO)</h2>
            <a href="create.php" class="btn btn-primary"><i class="bi bi-plus-circle"></i> Tạo SO</a>
        </div>
    </div>
</div>

<?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?php 
        if ($_GET['success'] == 'created') echo 'Tạo SO thành công!';
        if ($_GET['success'] == 'updated') echo 'Cập nhật SO thành công!';
        if ($_GET['success'] == 'deleted') echo 'Xóa SO thành công!';
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
                        <th>Số SO</th>
                        <th>Ngày SO</th>
                        <th>Khách hàng</th>
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
                            <td colspan="8" class="text-center">Chưa có đơn đặt hàng bán nào</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($orders as $order): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($order['so_number']); ?></td>
                                <td><?php echo Utils::formatDate($order['so_date']); ?></td>
                                <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
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
