<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo APP_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php">
                <i class="bi bi-file-earmark-text"></i> <?php echo APP_NAME; ?>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php"><i class="bi bi-house"></i> Dashboard</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="purchaseDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-cart-dash"></i> Quản lý Mua
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="modules/purchase_contracts/list.php">Hợp đồng mua</a></li>
                            <li><a class="dropdown-item" href="modules/purchase_orders/list.php">Đơn đặt hàng (PO)</a></li>
                            <li><a class="dropdown-item" href="modules/suppliers/list.php">Nhà cung cấp</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="salesDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-cart-plus"></i> Quản lý Bán
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="modules/sales_contracts/list.php">Hợp đồng bán</a></li>
                            <li><a class="dropdown-item" href="modules/sales_orders/list.php">Đơn đặt hàng (SO)</a></li>
                            <li><a class="dropdown-item" href="modules/customers/list.php">Khách hàng</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="reportsDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-bar-chart"></i> Báo cáo
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="modules/reports/contracts.php">Báo cáo hợp đồng</a></li>
                            <li><a class="dropdown-item" href="modules/reports/revenue.php">Báo cáo doanh thu</a></li>
                            <li><a class="dropdown-item" href="modules/reports/profit.php">Báo cáo lợi nhuận</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container-fluid mt-4">
