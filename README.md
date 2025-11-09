# Contract Management System (Hệ thống quản lý hợp đồng)

Hệ thống quản lý hợp đồng mua bán toàn diện được phát triển bằng PHP/MySQL, giúp doanh nghiệp quản lý hiệu quả các hợp đồng mua, hợp đồng bán, đơn đặt hàng và tài liệu liên quan.

## Tính năng chính

### 1. Quản lý hợp đồng
- **Hợp đồng mua (Purchase Contracts)**: Quản lý hợp đồng mua sản phẩm, hàng hóa, dịch vụ từ nhà cung cấp
- **Hợp đồng bán (Sales Contracts)**: Quản lý hợp đồng bán sản phẩm, hàng hóa, dịch vụ cho khách hàng
- Tự động đánh số hợp đồng theo quy tắc: Loại-Năm-TậpHồSơ-SốThứTự (VD: PC-2024-A1-0001)
- Quản lý trạng thái hợp đồng: Nháp, Đang hiệu lực, Hoàn thành, Đã hủy
- Lưu trữ thông tin chi tiết: ngày hợp đồng, giá trị, điều khoản thanh toán, ghi chú

### 2. Quản lý đơn đặt hàng
- **Đơn đặt hàng mua (Purchase Orders - PO)**: Quản lý đơn đặt hàng từ nhà cung cấp
- **Đơn đặt hàng bán (Sales Orders - SO)**: Quản lý đơn đặt hàng cho khách hàng
- Liên kết đơn hàng với hợp đồng tương ứng
- Đánh số tự động theo năm và loại đơn hàng

### 3. Quản lý đối tác
- **Nhà cung cấp (Suppliers)**: Quản lý thông tin chi tiết nhà cung cấp
- **Khách hàng (Customers)**: Quản lý thông tin chi tiết khách hàng
- Lưu trữ: mã số, tên, người liên hệ, thông tin liên lạc, mã số thuế, thông tin ngân hàng

### 4. Quản lý tài liệu
- Tự động tạo thư mục cho mỗi hợp đồng theo cấu trúc: Năm/KhachHang/SoHopDong/
- Hỗ trợ upload đa dạng loại tài liệu: chào giá, hợp đồng, biên bản bàn giao, đề nghị thanh toán
- Quản lý file đính kèm với metadata đầy đủ

### 5. Báo cáo và Dashboard
- **Dashboard**: Tổng hợp số lượng hợp đồng, đơn hàng, doanh thu, lợi nhuận
- **Báo cáo hợp đồng**: Lọc theo loại, năm, trạng thái
- **Báo cáo doanh thu**: Theo tháng/quý/năm với phân tích lợi nhuận
- **Báo cáo lợi nhuận**: Tính toán tỷ suất lợi nhuận, so sánh doanh thu và chi phí
- Xuất báo cáo PDF/in trực tiếp

### 6. Hệ thống đánh số
- Đánh số tự động theo năm và tập hồ sơ
- Hỗ trợ nhiều tập hồ sơ trong cùng một năm
- Đảm bảo số thứ tự liên tục và không trùng lặp
- Format: PREFIX-YEAR-ARCHIVE-NUMBER (VD: SC-2024-A1-0001)

## Yêu cầu hệ thống

- PHP 7.4 trở lên
- MySQL 5.7 trở lên / MariaDB 10.2 trở lên
- Apache/Nginx Web Server
- PDO Extension
- GD Library (cho xử lý ảnh nếu cần)

## Cài đặt

### 1. Clone hoặc tải về source code

```bash
git clone https://github.com/vuvannamviet-dev/contract-management.git
cd contract-management
```

### 2. Cấu hình database

Mở file `config/database.php` và chỉnh sửa thông tin kết nối:

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'contract_management');
```

### 3. Tạo database

Import file schema vào MySQL:

```bash
mysql -u root -p < database/schema.sql
```

Hoặc sử dụng phpMyAdmin:
1. Tạo database mới tên `contract_management`
2. Import file `database/schema.sql`

### 4. Cấu hình upload directory

Đảm bảo thư mục `uploads/contracts` có quyền ghi:

```bash
chmod -R 755 uploads/
```

### 5. Cấu hình web server

**Apache:**
- Copy source code vào thư mục `htdocs` hoặc `www`
- Truy cập: `http://localhost/contract-management`

**Nginx:**
- Cấu hình virtual host trỏ đến thư mục source code
- Đảm bảo index.php là file mặc định

### 6. Truy cập hệ thống

Mở trình duyệt và truy cập:
```
http://localhost/contract-management
```

## Hướng dẫn sử dụng

### Bước 1: Thêm nhà cung cấp và khách hàng

1. Vào menu **Quản lý Mua** → **Nhà cung cấp** → **Thêm nhà cung cấp**
2. Nhập thông tin: Mã NCC, Tên, Người liên hệ, Thông tin liên lạc
3. Vào menu **Quản lý Bán** → **Khách hàng** → **Thêm khách hàng**
4. Nhập thông tin tương tự

### Bước 2: Tạo hợp đồng mua

1. Vào menu **Quản lý Mua** → **Hợp đồng mua** → **Tạo hợp đồng mua**
2. Chọn nhà cung cấp từ dropdown
3. Nhập ngày hợp đồng (hệ thống tự tạo số HĐ)
4. Nhập tiêu đề, giá trị, điều khoản
5. Có thể nhập số tập hồ sơ (VD: A1, B2) để phân loại
6. Lưu hợp đồng

### Bước 3: Tạo hợp đồng bán

1. Vào menu **Quản lý Bán** → **Hợp đồng bán** → **Tạo hợp đồng bán**
2. Chọn khách hàng từ dropdown
3. Nhập thông tin hợp đồng
4. Nhập **Giá trị hợp đồng** và **Chi phí (giá vốn)** để tính lợi nhuận
5. Lưu hợp đồng

### Bước 4: Quản lý tài liệu

1. Sau khi tạo hợp đồng, hệ thống tự động tạo thư mục trong `uploads/contracts/[Năm]/[KhachHang]/[SoHD]/`
2. Upload tài liệu liên quan: chào giá, hợp đồng ký kết, biên bản...
3. Tất cả tài liệu được lưu trong cùng folder cho dễ quản lý

### Bước 5: Xem báo cáo

1. **Dashboard**: Xem tổng quan doanh thu, lợi nhuận, số lượng hợp đồng
2. **Báo cáo hợp đồng**: Lọc và xem danh sách hợp đồng theo điều kiện
3. **Báo cáo doanh thu**: Phân tích doanh thu theo tháng/quý/năm
4. **Báo cáo lợi nhuận**: So sánh doanh thu - chi phí, tính tỷ suất lợi nhuận

## Cấu trúc thư mục

```
contract-management/
├── config/              # Cấu hình database và ứng dụng
├── database/            # Database schema và migrations
├── includes/            # Các file chung (header, footer, utils)
├── modules/             # Các module chức năng
│   ├── suppliers/       # Quản lý nhà cung cấp
│   ├── customers/       # Quản lý khách hàng
│   ├── purchase_contracts/  # Quản lý hợp đồng mua
│   ├── sales_contracts/     # Quản lý hợp đồng bán
│   ├── purchase_orders/     # Quản lý đơn đặt hàng mua
│   ├── sales_orders/        # Quản lý đơn đặt hàng bán
│   └── reports/         # Báo cáo
├── assets/              # CSS, JS, images
│   ├── css/
│   └── js/
├── uploads/             # Thư mục lưu file upload
│   └── contracts/       # Tài liệu hợp đồng
└── index.php            # Trang chủ (Dashboard)
```

## Cấu trúc database

### Bảng chính:
- **suppliers**: Nhà cung cấp
- **customers**: Khách hàng
- **purchase_contracts**: Hợp đồng mua
- **sales_contracts**: Hợp đồng bán
- **purchase_orders**: Đơn đặt hàng mua
- **sales_orders**: Đơn đặt hàng bán
- **contract_documents**: Tài liệu đính kèm
- **contract_items**: Chi tiết sản phẩm/dịch vụ trong hợp đồng
- **numbering_sequences**: Quản lý số thứ tự hợp đồng

## Công nghệ sử dụng

- **Backend**: PHP với PDO
- **Database**: MySQL
- **Frontend**: Bootstrap 5, Bootstrap Icons
- **JavaScript**: Vanilla JS
- **Architecture**: MVC pattern (simplified)

## Bảo mật

- Sử dụng PDO Prepared Statements để phòng chống SQL Injection
- Sanitize và validate tất cả input từ người dùng
- Kiểm tra quyền truy cập file uploads
- Session management cho authentication (có thể mở rộng)

## Tính năng mở rộng

Có thể mở rộng thêm:
- Hệ thống đăng nhập/phân quyền
- Quản lý workflow phê duyệt hợp đồng
- Thông báo email tự động
- Export Excel/PDF cho báo cáo
- API REST cho tích hợp với hệ thống khác
- Multi-language support
- Mobile responsive optimization

## Đóng góp

Mọi đóng góp đều được chào đón! Vui lòng:
1. Fork repository
2. Tạo branch mới (`git checkout -b feature/AmazingFeature`)
3. Commit changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to branch (`git push origin feature/AmazingFeature`)
5. Tạo Pull Request

## License

Dự án này được phát hành dưới giấy phép MIT License.

## Liên hệ

Nếu có câu hỏi hoặc cần hỗ trợ, vui lòng tạo issue trên GitHub.

## Demo Data

Hệ thống đã được tạo sẵn một số dữ liệu mẫu:
- 3 nhà cung cấp mẫu
- 3 khách hàng mẫu

Bạn có thể sử dụng để test hoặc xóa đi và nhập dữ liệu thật.