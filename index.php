<?php
require_once 'config/config.php';
require_once 'config/database.php';
require_once 'includes/utils.php';

// Get dashboard statistics
$db = new Database();
$conn = $db->connect();

// Get counts
$stats = [
    'purchase_contracts' => 0,
    'sales_contracts' => 0,
    'purchase_orders' => 0,
    'sales_orders' => 0,
    'suppliers' => 0,
    'customers' => 0
];

try {
    $stmt = $conn->query("SELECT COUNT(*) as count FROM purchase_contracts WHERE status != 'cancelled'");
    $stats['purchase_contracts'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    $stmt = $conn->query("SELECT COUNT(*) as count FROM sales_contracts WHERE status != 'cancelled'");
    $stats['sales_contracts'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    $stmt = $conn->query("SELECT COUNT(*) as count FROM purchase_orders WHERE status != 'cancelled'");
    $stats['purchase_orders'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    $stmt = $conn->query("SELECT COUNT(*) as count FROM sales_orders WHERE status != 'cancelled'");
    $stats['sales_orders'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    $stmt = $conn->query("SELECT COUNT(*) as count FROM suppliers");
    $stats['suppliers'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    $stmt = $conn->query("SELECT COUNT(*) as count FROM customers");
    $stats['customers'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Get financial summary for current year
    $currentYear = date('Y');
    
    $stmt = $conn->prepare("SELECT SUM(total_amount) as total FROM purchase_contracts 
                           WHERE contract_year = :year AND status = 'active'");
    $stmt->execute([':year' => $currentYear]);
    $purchaseTotal = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
    
    $stmt = $conn->prepare("SELECT SUM(total_amount) as total FROM sales_contracts 
                           WHERE contract_year = :year AND status = 'active'");
    $stmt->execute([':year' => $currentYear]);
    $salesTotal = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
    
    $stmt = $conn->prepare("SELECT SUM(cost_amount) as total FROM sales_contracts 
                           WHERE contract_year = :year AND status = 'active'");
    $stmt->execute([':year' => $currentYear]);
    $costTotal = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
    
    $profit = $salesTotal - $costTotal;
    
} catch(PDOException $e) {
    $error = "Database error: " . $e->getMessage();
}

include 'includes/header.php';
?>

<div class="row">
    <div class="col-12">
        <h1 class="mb-4"><i class="bi bi-speedometer2"></i> Dashboard</h1>
    </div>
</div>

<?php if (isset($error)): ?>
    <div class="alert alert-danger"><?php echo $error; ?></div>
<?php endif; ?>

<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <h5 class="card-title"><i class="bi bi-file-earmark-text"></i> Hợp đồng mua</h5>
                <h2><?php echo $stats['purchase_contracts']; ?></h2>
                <a href="modules/purchase_contracts/list.php" class="text-white">Xem chi tiết <i class="bi bi-arrow-right"></i></a>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <h5 class="card-title"><i class="bi bi-file-earmark-text"></i> Hợp đồng bán</h5>
                <h2><?php echo $stats['sales_contracts']; ?></h2>
                <a href="modules/sales_contracts/list.php" class="text-white">Xem chi tiết <i class="bi bi-arrow-right"></i></a>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body">
                <h5 class="card-title"><i class="bi bi-cart-dash"></i> Đơn đặt hàng mua</h5>
                <h2><?php echo $stats['purchase_orders']; ?></h2>
                <a href="modules/purchase_orders/list.php" class="text-white">Xem chi tiết <i class="bi bi-arrow-right"></i></a>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <h5 class="card-title"><i class="bi bi-cart-plus"></i> Đơn đặt hàng bán</h5>
                <h2><?php echo $stats['sales_orders']; ?></h2>
                <a href="modules/sales_orders/list.php" class="text-white">Xem chi tiết <i class="bi bi-arrow-right"></i></a>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="bi bi-cash-stack"></i> Doanh thu năm <?php echo $currentYear; ?></h5>
            </div>
            <div class="card-body">
                <h3><?php echo Utils::formatCurrency($salesTotal); ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="bi bi-graph-up"></i> Lợi nhuận năm <?php echo $currentYear; ?></h5>
            </div>
            <div class="card-body">
                <h3><?php echo Utils::formatCurrency($profit); ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-warning text-white">
                <h5 class="mb-0"><i class="bi bi-receipt"></i> Chi phí năm <?php echo $currentYear; ?></h5>
            </div>
            <div class="card-body">
                <h3><?php echo Utils::formatCurrency($purchaseTotal); ?></h3>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-building"></i> Nhà cung cấp & Khách hàng</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-6">
                        <h6>Nhà cung cấp</h6>
                        <h3><?php echo $stats['suppliers']; ?></h3>
                        <a href="modules/suppliers/list.php">Quản lý <i class="bi bi-arrow-right"></i></a>
                    </div>
                    <div class="col-6">
                        <h6>Khách hàng</h6>
                        <h3><?php echo $stats['customers']; ?></h3>
                        <a href="modules/customers/list.php">Quản lý <i class="bi bi-arrow-right"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-info-circle"></i> Hướng dẫn sử dụng</h5>
            </div>
            <div class="card-body">
                <ul>
                    <li>Quản lý nhà cung cấp và khách hàng trước khi tạo hợp đồng</li>
                    <li>Tạo hợp đồng mua/bán với mã số tự động theo năm</li>
                    <li>Đính kèm tài liệu vào hợp đồng (chào giá, biên bản...)</li>
                    <li>Tạo đơn đặt hàng (PO/SO) liên kết với hợp đồng</li>
                    <li>Xem báo cáo doanh thu và lợi nhuận theo thời gian</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
