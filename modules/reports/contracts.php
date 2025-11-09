<?php
require_once '../../config/config.php';
require_once '../../config/database.php';
require_once '../../includes/utils.php';

$db = new Database();
$conn = $db->connect();

// Get filter parameters
$type = isset($_GET['type']) ? $_GET['type'] : 'all';
$year = isset($_GET['year']) ? intval($_GET['year']) : date('Y');
$status = isset($_GET['status']) ? $_GET['status'] : 'all';

// Build query based on filters
if ($type == 'purchase' || $type == 'all') {
    $sql_purchase = "SELECT 
                        'Mua' as type,
                        pc.contract_number,
                        pc.contract_date,
                        s.name as partner_name,
                        pc.title,
                        pc.total_amount,
                        pc.currency,
                        pc.status
                     FROM purchase_contracts pc
                     LEFT JOIN suppliers s ON pc.supplier_id = s.id
                     WHERE pc.contract_year = :year";
    
    if ($status != 'all') {
        $sql_purchase .= " AND pc.status = :status";
    }
}

if ($type == 'sales' || $type == 'all') {
    $sql_sales = "SELECT 
                      'Bán' as type,
                      sc.contract_number,
                      sc.contract_date,
                      c.name as partner_name,
                      sc.title,
                      sc.total_amount,
                      sc.currency,
                      sc.status
                   FROM sales_contracts sc
                   LEFT JOIN customers c ON sc.customer_id = c.id
                   WHERE sc.contract_year = :year";
    
    if ($status != 'all') {
        $sql_sales .= " AND sc.status = :status";
    }
}

// Execute queries
$contracts = [];

if (isset($sql_purchase)) {
    $stmt = $conn->prepare($sql_purchase);
    $params = [':year' => $year];
    if ($status != 'all') $params[':status'] = $status;
    $stmt->execute($params);
    $contracts = array_merge($contracts, $stmt->fetchAll(PDO::FETCH_ASSOC));
}

if (isset($sql_sales)) {
    $stmt = $conn->prepare($sql_sales);
    $params = [':year' => $year];
    if ($status != 'all') $params[':status'] = $status;
    $stmt->execute($params);
    $contracts = array_merge($contracts, $stmt->fetchAll(PDO::FETCH_ASSOC));
}

// Sort by date
usort($contracts, function($a, $b) {
    return strtotime($b['contract_date']) - strtotime($a['contract_date']);
});

// Get years for dropdown
$stmt = $conn->query("SELECT DISTINCT contract_year FROM purchase_contracts 
                      UNION SELECT DISTINCT contract_year FROM sales_contracts 
                      ORDER BY contract_year DESC");
$years = $stmt->fetchAll(PDO::FETCH_COLUMN);

include '../../includes/header.php';
?>

<div class="row">
    <div class="col-12">
        <h2><i class="bi bi-file-earmark-text"></i> Báo cáo hợp đồng</h2>
    </div>
</div>

<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-3">
                <label for="type" class="form-label">Loại hợp đồng</label>
                <select class="form-select" id="type" name="type">
                    <option value="all" <?php echo $type == 'all' ? 'selected' : ''; ?>>Tất cả</option>
                    <option value="purchase" <?php echo $type == 'purchase' ? 'selected' : ''; ?>>Hợp đồng mua</option>
                    <option value="sales" <?php echo $type == 'sales' ? 'selected' : ''; ?>>Hợp đồng bán</option>
                </select>
            </div>
            <div class="col-md-3">
                <label for="year" class="form-label">Năm</label>
                <select class="form-select" id="year" name="year">
                    <?php foreach ($years as $y): ?>
                        <option value="<?php echo $y; ?>" <?php echo $y == $year ? 'selected' : ''; ?>><?php echo $y; ?></option>
                    <?php endforeach; ?>
                    <?php if (empty($years)): ?>
                        <option value="<?php echo date('Y'); ?>" selected><?php echo date('Y'); ?></option>
                    <?php endif; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label for="status" class="form-label">Trạng thái</label>
                <select class="form-select" id="status" name="status">
                    <option value="all" <?php echo $status == 'all' ? 'selected' : ''; ?>>Tất cả</option>
                    <option value="draft" <?php echo $status == 'draft' ? 'selected' : ''; ?>>Nháp</option>
                    <option value="active" <?php echo $status == 'active' ? 'selected' : ''; ?>>Đang hiệu lực</option>
                    <option value="completed" <?php echo $status == 'completed' ? 'selected' : ''; ?>>Hoàn thành</option>
                    <option value="cancelled" <?php echo $status == 'cancelled' ? 'selected' : ''; ?>>Đã hủy</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">&nbsp;</label>
                <button type="submit" class="btn btn-primary d-block w-100"><i class="bi bi-search"></i> Lọc</button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Danh sách hợp đồng (<?php echo count($contracts); ?> hợp đồng)</h5>
        <button onclick="window.print()" class="btn btn-sm btn-secondary no-print"><i class="bi bi-printer"></i> In báo cáo</button>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover table-sm">
                <thead>
                    <tr>
                        <th>Loại</th>
                        <th>Số HĐ</th>
                        <th>Ngày HĐ</th>
                        <th>Đối tác</th>
                        <th>Tiêu đề</th>
                        <th class="text-end">Giá trị</th>
                        <th>Trạng thái</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($contracts)): ?>
                        <tr>
                            <td colspan="7" class="text-center">Không có hợp đồng nào</td>
                        </tr>
                    <?php else: ?>
                        <?php 
                        $total_amount = 0;
                        foreach ($contracts as $contract): 
                            $total_amount += $contract['total_amount'];
                        ?>
                            <tr>
                                <td>
                                    <?php if ($contract['type'] == 'Mua'): ?>
                                        <span class="badge bg-primary">Mua</span>
                                    <?php else: ?>
                                        <span class="badge bg-success">Bán</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($contract['contract_number']); ?></td>
                                <td><?php echo Utils::formatDate($contract['contract_date']); ?></td>
                                <td><?php echo htmlspecialchars($contract['partner_name']); ?></td>
                                <td><?php echo htmlspecialchars($contract['title']); ?></td>
                                <td class="text-end"><?php echo Utils::formatCurrency($contract['total_amount'], $contract['currency']); ?></td>
                                <td><?php echo Utils::getStatusBadge($contract['status']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <tr class="table-primary fw-bold">
                            <td colspan="5">Tổng giá trị</td>
                            <td class="text-end"><?php echo Utils::formatCurrency($total_amount); ?></td>
                            <td></td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
