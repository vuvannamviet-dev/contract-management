# Tính năng chi tiết / Features Documentation

## 1. Quản lý Nhà cung cấp (Supplier Management)

### Chức năng
- Thêm mới nhà cung cấp với đầy đủ thông tin
- Xem danh sách tất cả nhà cung cấp
- Sửa thông tin nhà cung cấp
- Xóa nhà cung cấp (có kiểm tra ràng buộc với hợp đồng)

### Thông tin quản lý
- Mã nhà cung cấp (duy nhất)
- Tên công ty
- Người liên hệ
- Số điện thoại
- Email
- Địa chỉ
- Mã số thuế
- Số tài khoản ngân hàng
- Tên ngân hàng
- Ghi chú

## 2. Quản lý Khách hàng (Customer Management)

### Chức năng
- Thêm mới khách hàng
- Xem danh sách khách hàng
- Cập nhật thông tin
- Xóa khách hàng (có bảo vệ dữ liệu)

### Thông tin quản lý
- Mã khách hàng (duy nhất)
- Tên công ty/cá nhân
- Người liên hệ
- Thông tin liên lạc (phone, email)
- Địa chỉ đầy đủ
- Mã số thuế
- Thông tin ngân hàng
- Ghi chú riêng

## 3. Hợp đồng mua (Purchase Contracts)

### Chức năng
- Tạo hợp đồng mua mới
- Tự động sinh số hợp đồng theo quy tắc
- Liên kết với nhà cung cấp
- Quản lý trạng thái hợp đồng
- Xem chi tiết đầy đủ
- Chỉnh sửa thông tin

### Quy tắc đánh số
Format: `PC-[Năm]-[TậpHồSơ]-[SốThứTự]`
- PC: Purchase Contract
- Năm: Năm của hợp đồng (YYYY)
- Tập hồ sơ: Tùy chọn (A1, B2, etc.)
- Số thứ tự: Tự động tăng (0001, 0002...)

Ví dụ: `PC-2024-A1-0001`, `PC-2024-0001`

### Thông tin hợp đồng
- Số hợp đồng (auto-generated)
- Nhà cung cấp
- Ngày hợp đồng
- Năm hợp đồng
- Số tập hồ sơ (tùy chọn)
- Tiêu đề hợp đồng
- Mô tả chi tiết
- Giá trị hợp đồng
- Đơn vị tiền tệ (VND/USD/EUR)
- Trạng thái (Draft/Active/Completed/Cancelled)
- Ngày bắt đầu/kết thúc
- Điều khoản thanh toán
- Ghi chú
- Đường dẫn folder lưu trữ

### Quản lý folder
Tự động tạo cấu trúc folder:
```
uploads/contracts/
  └── 2024/
      └── Ten_Nha_Cung_Cap/
          └── PC-2024-A1-0001/
              ├── quotation.pdf
              ├── contract.pdf
              └── other_documents/
```

## 4. Hợp đồng bán (Sales Contracts)

### Chức năng
- Tạo hợp đồng bán
- Tự động tính lợi nhuận
- Liên kết khách hàng
- Quản lý đầy đủ thông tin
- Theo dõi chi phí và doanh thu

### Quy tắc đánh số
Format: `SC-[Năm]-[TậpHồSơ]-[SốThứTự]`
- SC: Sales Contract
- Tương tự như hợp đồng mua

### Thông tin đặc biệt
- Giá trị hợp đồng (doanh thu)
- Chi phí (giá vốn)
- Lợi nhuận = Doanh thu - Chi phí
- Tỷ suất lợi nhuận (%)

### Tính năng nổi bật
- Tự động tính toán lợi nhuận
- Hiển thị tỷ suất lợi nhuận
- Cảnh báo lỗ (hiển thị màu đỏ)
- Folder quản lý theo khách hàng

## 5. Đơn đặt hàng mua (Purchase Orders - PO)

### Chức năng
- Tạo PO mới
- Liên kết với nhà cung cấp
- Tùy chọn liên kết với hợp đồng mua
- Quản lý trạng thái PO

### Quy tắc đánh số
Format: `PO-[Năm]-[SốThứTự]`
Ví dụ: `PO-2024-0001`

### Thông tin PO
- Số PO (auto-generated)
- Nhà cung cấp
- Hợp đồng liên kết (optional)
- Ngày PO
- Tiêu đề
- Mô tả
- Giá trị
- Đơn vị tiền tệ
- Trạng thái (Draft/Submitted/Approved/Completed/Cancelled)
- Ngày giao hàng dự kiến
- Ghi chú

## 6. Đơn đặt hàng bán (Sales Orders - SO)

### Chức năng
- Tạo SO mới
- Liên kết với khách hàng
- Liên kết với hợp đồng bán
- Theo dõi trạng thái đơn hàng

### Quy tắc đánh số
Format: `SO-[Năm]-[SốThứTự]`
Ví dụ: `SO-2024-0001`

### Thông tin SO
Tương tự PO nhưng với khách hàng

## 7. Dashboard (Bảng điều khiển)

### Thống kê hiển thị
- Số lượng hợp đồng mua
- Số lượng hợp đồng bán
- Số lượng PO
- Số lượng SO
- Số lượng nhà cung cấp
- Số lượng khách hàng

### Thông tin tài chính (năm hiện tại)
- Tổng doanh thu (từ hợp đồng bán)
- Tổng lợi nhuận
- Tổng chi phí (từ hợp đồng mua)

### Tính năng
- Hiển thị realtime từ database
- Card với màu sắc phân biệt
- Link nhanh đến các module
- Hướng dẫn sử dụng

## 8. Báo cáo hợp đồng (Contract Reports)

### Tính năng lọc
- Theo loại hợp đồng (Mua/Bán/Tất cả)
- Theo năm
- Theo trạng thái

### Thông tin hiển thị
- Danh sách hợp đồng
- Thông tin đối tác
- Giá trị hợp đồng
- Trạng thái
- Tổng giá trị tất cả hợp đồng

### Chức năng
- In báo cáo
- Xuất dữ liệu (sẵn sàng)
- Sắp xếp theo các tiêu chí

## 9. Báo cáo doanh thu & lợi nhuận (Revenue & Profit Reports)

### Tính năng
- Báo cáo theo năm
- Báo cáo theo tháng
- Phân tích chi tiết

### Thông tin hiển thị
- Tổng doanh thu
- Tổng chi phí
- Lợi nhuận thuần
- Tỷ suất lợi nhuận (%)
- So sánh theo thời gian

### Phân tích
- Theo tháng trong năm
- Theo ngày trong tháng
- Số lượng hợp đồng trong kỳ
- Biểu đồ xu hướng (có thể mở rộng)

## 10. Hệ thống đánh số tự động (Numbering System)

### Đặc điểm
- Tự động tạo số thứ tự
- Không trùng lặp
- Quản lý riêng theo loại và năm
- Hỗ trợ tập hồ sơ

### Bảng quản lý
Table: `numbering_sequences`
- Lưu trữ số cuối cùng của mỗi loại
- Tự động tăng khi tạo mới
- Lock để tránh conflict

### Các loại hỗ trợ
- Purchase Contract (PC)
- Sales Contract (SC)
- Purchase Order (PO)
- Sales Order (SO)

## 11. Quản lý file & folder

### Tự động tạo folder
- Theo năm hợp đồng
- Theo tên đối tác
- Theo số hợp đồng

### Cấu trúc
```
uploads/contracts/
├── 2024/
│   ├── Customer_A/
│   │   ├── SC-2024-0001/
│   │   └── SC-2024-0002/
│   └── Supplier_B/
│       └── PC-2024-0001/
└── 2025/
    └── ...
```

### Bảo mật
- Folder được tạo với quyền 755
- Kiểm tra kích thước file upload
- Validate loại file (có thể mở rộng)
- Lưu metadata trong database

## 12. Bảo mật & Validation

### Input Validation
- Sanitize tất cả input từ form
- Validate required fields
- Check unique constraints (mã số)
- Validate email format
- Validate số điện thoại

### Database Security
- PDO Prepared Statements
- Prevent SQL Injection
- Transaction support (sẵn sàng)

### Output Security
- htmlspecialchars cho tất cả output
- XSS protection
- Safe file path handling

## 13. UI/UX Features

### Responsive Design
- Bootstrap 5 framework
- Mobile-friendly
- Tablet-optimized

### User Experience
- Breadcrumb navigation
- Success/error messages
- Confirm dialogs for delete
- Loading states
- Form validation feedback

### Visual Elements
- Bootstrap Icons
- Color-coded status badges
- Consistent layout
- Print-friendly styles

## Mở rộng tương lai

### Authentication & Authorization
- User login system
- Role-based permissions
- Session management
- Password encryption

### Advanced Features
- Document upload UI
- PDF generation
- Excel export
- Email notifications
- Workflow approval
- Audit trail
- API endpoints
- Multi-language
- Advanced search
- Batch operations

### Integration
- Email service (SMTP)
- Cloud storage
- Payment gateways
- ERP systems
- Accounting software

### Analytics
- Charts and graphs
- Trend analysis
- Forecasting
- KPI tracking
- Custom dashboards
