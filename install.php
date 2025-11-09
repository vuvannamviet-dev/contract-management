<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cài đặt - Contract Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h3>Contract Management System - Cài đặt</h3>
                    </div>
                    <div class="card-body">
                        <?php
                        require_once 'config/database.php';
                        
                        $message = '';
                        $error = '';
                        
                        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                            try {
                                // Read SQL file
                                $sql = file_get_contents(__DIR__ . '/database/schema.sql');
                                
                                // Connect without database name first
                                $conn = new PDO(
                                    'mysql:host=' . DB_HOST,
                                    DB_USER,
                                    DB_PASS
                                );
                                $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                                
                                // Execute SQL
                                $conn->exec($sql);
                                
                                $message = 'Cài đặt database thành công! Hệ thống đã sẵn sàng sử dụng.';
                                $message .= '<br><a href="index.php" class="btn btn-primary mt-3">Vào hệ thống</a>';
                                
                            } catch(PDOException $e) {
                                $error = 'Lỗi cài đặt: ' . $e->getMessage();
                                $error .= '<br><small>Vui lòng kiểm tra lại cấu hình database trong file config/database.php</small>';
                            }
                        }
                        ?>
                        
                        <?php if ($message): ?>
                            <div class="alert alert-success">
                                <?php echo $message; ?>
                            </div>
                        <?php elseif ($error): ?>
                            <div class="alert alert-danger">
                                <?php echo $error; ?>
                            </div>
                        <?php endif; ?>
                        
                        <h4>Hướng dẫn cài đặt</h4>
                        <ol>
                            <li>Đảm bảo MySQL đang chạy</li>
                            <li>Cấu hình thông tin database trong file <code>config/database.php</code>:
                                <ul>
                                    <li>DB_HOST: <?php echo DB_HOST; ?></li>
                                    <li>DB_USER: <?php echo DB_USER; ?></li>
                                    <li>DB_NAME: <?php echo DB_NAME; ?></li>
                                </ul>
                            </li>
                            <li>Nhấn nút "Cài đặt Database" bên dưới</li>
                            <li>Hệ thống sẽ tự động tạo database và các bảng cần thiết</li>
                        </ol>
                        
                        <form method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn cài đặt database? Thao tác này sẽ xóa database cũ nếu đã tồn tại!');">
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="bi bi-database"></i> Cài đặt Database
                                </button>
                            </div>
                        </form>
                        
                        <hr>
                        
                        <h5>Thông tin hệ thống</h5>
                        <ul>
                            <li>PHP Version: <?php echo phpversion(); ?></li>
                            <li>PDO MySQL: <?php echo extension_loaded('pdo_mysql') ? 'Đã cài đặt' : 'Chưa cài đặt'; ?></li>
                            <li>Upload Max Filesize: <?php echo ini_get('upload_max_filesize'); ?></li>
                            <li>Post Max Size: <?php echo ini_get('post_max_size'); ?></li>
                        </ul>
                        
                        <div class="alert alert-info mt-3">
                            <strong>Lưu ý:</strong> Sau khi cài đặt xong, bạn nên xóa hoặc đổi tên file <code>install.php</code> này để đảm bảo an toàn.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
