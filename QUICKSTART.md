# Quick Start Guide - Contract Management System

## Hướng dẫn nhanh để bắt đầu sử dụng hệ thống

### 1. Cài đặt (5 phút)

#### Bước 1: Tải source code
```bash
git clone https://github.com/vuvannamviet-dev/contract-management.git
cd contract-management
```

#### Bước 2: Cấu hình database
Mở file `config/database.php` và sửa thông tin:
```php
define('DB_HOST', 'localhost');  // Địa chỉ MySQL server
define('DB_USER', 'root');       // Username MySQL
define('DB_PASS', '');           // Password MySQL
define('DB_NAME', 'contract_management');
```

#### Bước 3: Cài đặt database
**Cách 1: Sử dụng trình duyệt (Đơn giản nhất)**
1. Copy source code vào htdocs hoặc www
2. Truy cập: `http://localhost/contract-management/install.php`
3. Nhấn nút "Cài đặt Database"
4. Xong! Hệ thống sẵn sàng

**Cách 2: Sử dụng MySQL command line**
```bash
mysql -u root -p < database/schema.sql
```

#### Bước 4: Truy cập hệ thống
```
http://localhost/contract-management
```

### 2. Sử dụng cơ bản (10 phút)

#### Tạo nhà cung cấp đầu tiên
1. Menu: **Quản lý Mua** → **Nhà cung cấp** → **Thêm nhà cung cấp**
2. Điền thông tin:
   - Mã NCC: `SUP001`
   - Tên: `Công ty ABC`
   - Điện thoại: `0901234567`
3. Nhấn **Lưu**

#### Tạo khách hàng đầu tiên
1. Menu: **Quản lý Bán** → **Khách hàng** → **Thêm khách hàng**
2. Điền thông tin:
   - Mã KH: `CUS001`
   - Tên: `Công ty XYZ`
   - Điện thoại: `0912345678`
3. Nhấn **Lưu**

#### Tạo hợp đồng mua đầu tiên
1. Menu: **Quản lý Mua** → **Hợp đồng mua** → **Tạo hợp đồng mua**
2. Chọn nhà cung cấp: `SUP001 - Công ty ABC`
3. Ngày hợp đồng: (chọn ngày)
4. Tiêu đề: `Mua thiết bị văn phòng`
5. Giá trị: `50000000`
6. Trạng thái: `Đang hiệu lực`
7. Nhấn **Tạo hợp đồng**

Số hợp đồng sẽ tự động được tạo, ví dụ: `PC-2024-0001`

#### Tạo hợp đồng bán đầu tiên
1. Menu: **Quản lý Bán** → **Hợp đồng bán** → **Tạo hợp đồng bán**
2. Chọn khách hàng: `CUS001 - Công ty XYZ`
3. Ngày hợp đồng: (chọn ngày)
4. Tiêu đề: `Cung cấp dịch vụ IT`
5. Giá trị hợp đồng: `100000000`
6. Chi phí (giá vốn): `60000000`
7. Trạng thái: `Đang hiệu lực`
8. Nhấn **Tạo hợp đồng**

Hệ thống tự động tính lợi nhuận: 100tr - 60tr = 40tr (40%)

### 3. Xem báo cáo

#### Dashboard
1. Nhấn vào logo hoặc **Dashboard** ở menu
2. Xem tổng quan:
   - Số lượng hợp đồng
   - Doanh thu năm nay
   - Lợi nhuận
   - Chi phí

#### Báo cáo hợp đồng
1. Menu: **Báo cáo** → **Báo cáo hợp đồng**
2. Chọn bộ lọc:
   - Loại: Tất cả / Mua / Bán
   - Năm: 2024
   - Trạng thái: Đang hiệu lực
3. Nhấn **Lọc**
4. Có thể in báo cáo bằng nút **In báo cáo**

#### Báo cáo doanh thu
1. Menu: **Báo cáo** → **Báo cáo doanh thu**
2. Chọn năm: 2024
3. (Tùy chọn) Chọn tháng cụ thể
4. Nhấn **Xem báo cáo**
5. Xem phân tích:
   - Doanh thu theo tháng
   - Chi phí
   - Lợi nhuận
   - Tỷ suất LN

### 4. Tính năng nâng cao

#### Sử dụng tập hồ sơ
Khi tạo hợp đồng, có thể nhập "Số tập hồ sơ" (ví dụ: A1, B2)
- Số HĐ sẽ là: `PC-2024-A1-0001`
- Giúp phân loại hợp đồng theo nhóm

#### Liên kết PO với hợp đồng
1. Tạo hợp đồng mua trước
2. Sau đó tạo PO
3. Chọn "Hợp đồng liên kết" → Chọn hợp đồng đã tạo
4. PO sẽ được liên kết với hợp đồng

#### Quản lý folder tài liệu
Sau khi tạo hợp đồng, hệ thống tự động tạo folder:
```
uploads/contracts/2024/Cong_ty_ABC/PC-2024-0001/
```

Bạn có thể:
1. Upload file vào folder này qua FTP/File Manager
2. Hoặc sử dụng tính năng upload (nếu đã triển khai)

### 5. Tips & Tricks

#### Tìm kiếm nhanh
- Sử dụng Ctrl+F trong danh sách để tìm kiếm
- Các bảng đều có thể sắp xếp

#### In báo cáo
- Tất cả báo cáo đều có nút "In báo cáo"
- Hoặc dùng Ctrl+P
- Layout đã tối ưu cho in ấn

#### Backup dữ liệu
Export database thường xuyên:
```bash
mysqldump -u root -p contract_management > backup.sql
```

#### Xóa dữ liệu mẫu
Nếu muốn xóa 3 nhà cung cấp và 3 khách hàng mẫu:
1. Vào từng trang danh sách
2. Nhấn nút xóa (biểu tượng thùng rác)
3. Xác nhận

### 6. Troubleshooting

#### Lỗi kết nối database
- Kiểm tra MySQL đã chạy chưa
- Xem lại thông tin trong `config/database.php`
- Kiểm tra username/password MySQL

#### Không tạo được folder
- Kiểm tra quyền của thư mục `uploads/contracts`
- Chạy: `chmod -R 755 uploads/`

#### Không hiển thị tiếng Việt
- Kiểm tra database charset: utf8mb4
- Kiểm tra file PHP đã save dạng UTF-8

#### Lỗi "Number already exists"
- Có thể do chạy đồng thời
- Thử lại sau vài giây
- Hoặc reset numbering sequence trong database

### 7. Câu hỏi thường gặp (FAQ)

**Q: Làm sao để thay đổi định dạng số hợp đồng?**
A: Sửa trong file `includes/utils.php`, hàm `generateContractNumber()`

**Q: Có thể thay đổi đơn vị tiền tệ mặc định?**
A: Có, sửa trong form tạo hợp đồng, hoặc thêm vào `config/config.php`

**Q: Làm sao để xóa hợp đồng?**
A: Vào danh sách hợp đồng → Nhấn nút xóa (thùng rác) → Xác nhận

**Q: Có giới hạn số lượng hợp đồng?**
A: Không, hệ thống có thể quản lý hàng ngàn hợp đồng

**Q: Hỗ trợ nhiều công ty?**
A: Hiện tại chưa, có thể mở rộng thêm tính năng multi-tenant

**Q: Có thể export sang Excel?**
A: Chưa có sẵn, nhưng có thể dễ dàng thêm bằng thư viện PHPExcel

### 8. Tiếp theo

Sau khi làm quen với các tính năng cơ bản, bạn có thể:

1. **Tùy chỉnh giao diện**: Sửa file CSS trong `assets/css/style.css`
2. **Thêm tính năng mới**: Tham khảo cấu trúc code hiện tại
3. **Tích hợp email**: Thêm PHPMailer để gửi thông báo
4. **Thêm authentication**: Xây dựng hệ thống đăng nhập
5. **API integration**: Tạo REST API cho mobile app

### 9. Hỗ trợ

Nếu gặp vấn đề:
1. Xem file README.md và FEATURES.md
2. Kiểm tra CHANGELOG.md cho các cập nhật
3. Tạo issue trên GitHub
4. Liên hệ qua email support

### 10. Checklist hoàn thành cài đặt

- [ ] Đã clone/download source code
- [ ] Đã cấu hình database
- [ ] Đã chạy install.php hoặc import schema.sql
- [ ] Đã truy cập được trang chủ
- [ ] Đã tạo được nhà cung cấp và khách hàng
- [ ] Đã tạo được hợp đồng mua
- [ ] Đã tạo được hợp đồng bán
- [ ] Đã xem được Dashboard và báo cáo
- [ ] Đã kiểm tra folder uploads/contracts

**Chúc bạn sử dụng hệ thống hiệu quả!**
