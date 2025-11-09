<?php
require_once '../../config/config.php';
require_once '../../config/database.php';
require_once '../../includes/utils.php';

$db = new Database();
$conn = $db->connect();

// Get all suppliers
$sql = "SELECT * FROM suppliers ORDER BY created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->execute();
$suppliers = $stmt->fetchAll(PDO::FETCH_ASSOC);

include '../../includes/header.php';
?>

<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="bi bi-building"></i> Danh sách nhà cung cấp</h2>
            <a href="create.php" class="btn btn-primary"><i class="bi bi-plus-circle"></i> Thêm nhà cung cấp</a>
        </div>
    </div>
</div>

<?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?php 
        if ($_GET['success'] == 'created') echo 'Thêm nhà cung cấp thành công!';
        if ($_GET['success'] == 'updated') echo 'Cập nhật nhà cung cấp thành công!';
        if ($_GET['success'] == 'deleted') echo 'Xóa nhà cung cấp thành công!';
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
                        <th>Mã NCC</th>
                        <th>Tên nhà cung cấp</th>
                        <th>Người liên hệ</th>
                        <th>Điện thoại</th>
                        <th>Email</th>
                        <th>Mã số thuế</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($suppliers)): ?>
                        <tr>
                            <td colspan="7" class="text-center">Chưa có nhà cung cấp nào</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($suppliers as $supplier): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($supplier['code']); ?></td>
                                <td><?php echo htmlspecialchars($supplier['name']); ?></td>
                                <td><?php echo htmlspecialchars($supplier['contact_person']); ?></td>
                                <td><?php echo htmlspecialchars($supplier['phone']); ?></td>
                                <td><?php echo htmlspecialchars($supplier['email']); ?></td>
                                <td><?php echo htmlspecialchars($supplier['tax_code']); ?></td>
                                <td class="action-buttons">
                                    <a href="view.php?id=<?php echo $supplier['id']; ?>" class="btn btn-sm btn-info" title="Xem">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="edit.php?id=<?php echo $supplier['id']; ?>" class="btn btn-sm btn-warning" title="Sửa">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <a href="delete.php?id=<?php echo $supplier['id']; ?>" class="btn btn-sm btn-danger btn-delete" title="Xóa">
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
