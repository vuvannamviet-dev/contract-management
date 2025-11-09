<?php
require_once '../../config/config.php';
require_once '../../config/database.php';
require_once '../../includes/utils.php';

$db = new Database();
$conn = $db->connect();

// Get filter parameters
$year = isset($_GET['year']) ? intval($_GET['year']) : date('Y');
$month = isset($_GET['month']) ? intval($_GET['month']) : null;

// Get revenue data
$revenue_data = [];
$total_revenue = 0;
$total_cost = 0;
$total_profit = 0;

if ($month) {
    // Monthly report
    $sql = "SELECT 
                DATE(contract_date) as period,
                SUM(total_amount) as revenue,
                SUM(cost_amount) as cost,
                SUM(total_amount - cost_amount) as profit,
                COUNT(*) as contract_count
            FROM sales_contracts
            WHERE contract_year = :year 
            AND MONTH(contract_date) = :month
            AND status = 'active'
            GROUP BY DATE(contract_date)
            ORDER BY period";
    $stmt = $conn->prepare($sql);
    $stmt->execute([':year' => $year, ':month' => $month]);
} else {
    // Yearly report by month
    $sql = "SELECT 
                MONTH(contract_date) as month_num,
                SUM(total_amount) as revenue,
                SUM(cost_amount) as cost,
                SUM(total_amount - cost_amount) as profit,
                COUNT(*) as contract_count
            FROM sales_contracts
            WHERE contract_year = :year
            AND status = 'active'
            GROUP BY MONTH(contract_date)
            ORDER BY month_num";
    $stmt = $conn->prepare($sql);
    $stmt->execute([':year' => $year]);
}

$revenue_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate totals
foreach ($revenue_data as $row) {
    $total_revenue += $row['revenue'];
    $total_cost += $row['cost'];
    $total_profit += $row['profit'];
}

// Get years for dropdown
$stmt = $conn->query("SELECT DISTINCT contract_year FROM sales_contracts ORDER BY contract_year DESC");
$years = $stmt->fetchAll(PDO::FETCH_COLUMN);

include '../../includes/header.php';
?>

<div class="row">
    <div class="col-12">
        <h2><i class="bi bi-bar-chart"></i> Báo cáo doanh thu và lợi nhuận</h2>
    </div>
</div>

<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-4">
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
            <div class="col-md-4">
                <label for="month" class="form-label">Tháng (tùy chọn)</label>
                <select class="form-select" id="month" name="month">
                    <option value="">Tất cả các tháng</option>
                    <?php for ($m = 1; $m <= 12; $m++): ?>
                        <option value="<?php echo $m; ?>" <?php echo $m == $month ? 'selected' : ''; ?>>Tháng <?php echo $m; ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">&nbsp;</label>
                <button type="submit" class="btn btn-primary d-block w-100"><i class="bi bi-search"></i> Xem báo cáo</button>
            </div>
        </form>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-4">
        <div class="card bg-success text-white">
            <div class="card-body">
                <h5>Tổng doanh thu</h5>
                <h3><?php echo Utils::formatCurrency($total_revenue); ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <h5>Tổng chi phí</h5>
                <h3><?php echo Utils::formatCurrency($total_cost); ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <h5>Lợi nhuận</h5>
                <h3><?php echo Utils::formatCurrency($total_profit); ?></h3>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Chi tiết <?php echo $month ? "tháng $month/$year" : "năm $year"; ?></h5>
        <button onclick="window.print()" class="btn btn-sm btn-secondary no-print"><i class="bi bi-printer"></i> In báo cáo</button>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th><?php echo $month ? 'Ngày' : 'Tháng'; ?></th>
                        <th>Số HĐ</th>
                        <th class="text-end">Doanh thu</th>
                        <th class="text-end">Chi phí</th>
                        <th class="text-end">Lợi nhuận</th>
                        <th class="text-end">Tỷ suất LN (%)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($revenue_data)): ?>
                        <tr>
                            <td colspan="6" class="text-center">Không có dữ liệu</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($revenue_data as $row): ?>
                            <?php 
                            $profit_margin = $row['revenue'] > 0 ? ($row['profit'] / $row['revenue']) * 100 : 0;
                            ?>
                            <tr>
                                <td>
                                    <?php 
                                    if ($month) {
                                        echo Utils::formatDate($row['period']);
                                    } else {
                                        echo 'Tháng ' . $row['month_num'];
                                    }
                                    ?>
                                </td>
                                <td><?php echo $row['contract_count']; ?> hợp đồng</td>
                                <td class="text-end"><?php echo Utils::formatCurrency($row['revenue']); ?></td>
                                <td class="text-end"><?php echo Utils::formatCurrency($row['cost']); ?></td>
                                <td class="text-end <?php echo $row['profit'] >= 0 ? 'text-success' : 'text-danger'; ?>">
                                    <?php echo Utils::formatCurrency($row['profit']); ?>
                                </td>
                                <td class="text-end <?php echo $profit_margin >= 0 ? 'text-success' : 'text-danger'; ?>">
                                    <?php echo number_format($profit_margin, 2); ?>%
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <tr class="table-primary fw-bold">
                            <td colspan="2">Tổng cộng</td>
                            <td class="text-end"><?php echo Utils::formatCurrency($total_revenue); ?></td>
                            <td class="text-end"><?php echo Utils::formatCurrency($total_cost); ?></td>
                            <td class="text-end <?php echo $total_profit >= 0 ? 'text-success' : 'text-danger'; ?>">
                                <?php echo Utils::formatCurrency($total_profit); ?>
                            </td>
                            <td class="text-end">
                                <?php echo $total_revenue > 0 ? number_format(($total_profit / $total_revenue) * 100, 2) : '0.00'; ?>%
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
