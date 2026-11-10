# Database cửa hàng đồ chơi

Database trên máy hiện tại đã được cập nhật, không cần chạy lại migration.

- Cài mới: tạo database `dochoi` trống rồi import `dochoi.sql` ở thư mục gốc.
- Mỗi sản phẩm có một giá và một số lượng tồn kho, không có màu/size.
- Đặt hàng trừ kho trong transaction; hủy đơn hoàn kho một lần.
- Hóa đơn cũ giữ nguyên mã đơn, mã sản phẩm, giá, số lượng và trạng thái.
- Giỏ hàng cũ được tự động gộp theo mã sản phẩm khi mở lại website.

`20261106_remove_product_variants.php` là migration CLI cho database từ phiên bản trước đã có `da_tru_kho` và trạng thái chuẩn hóa. Script tạo bản sao lưu SQL trong `backups/`, bỏ bảng/cột thuộc tính rồi xuất lại `dochoi.sql`. Không chạy script này trong khi website đang nhận đơn hoặc admin đang cập nhật dữ liệu.

Các tệp SQL của phiên bản quần áo được lưu trong `backups/legacy_clothing_sql.zip`. Bản sao lưu đầy đủ trước khi bỏ thuộc tính có tên `backups/dochoi_before_variants_*.sql`.

Luồng trạng thái hiện tại: Chờ xác nhận → Đã xác nhận → Đang giao hàng → Hoàn thành. Chờ xác nhận và Đang giao hàng được chuyển sang Hủy đơn, bắt buộc nhập lý do. Mỗi lần chỉ được đi một bước; đơn kết thúc không được mở lại. Migration `20261106_order_lifecycle.sql` đổi tên trạng thái cũ, không thay đổi tồn kho.

Cột `hoadon.ly_do_huy` lưu lý do hủy (tối đa 1000 ký tự). Migration bổ sung: `20261106_cancel_reason.sql`. Đơn hủy cũ giữ NULL vì trước đây chưa thu thập lý do.

Tài khoản quản trị viên và khách hàng dùng chung duy nhất bảng `taikhoan`, phân biệt bằng cột `role`. Bảng `admin` đã được gộp và bỏ khỏi database. Hồ sơ trong `khach_hang` được giữ lại để bảo toàn dữ liệu khách hàng và hóa đơn cũ. Migration CLI `20261106_admin_accounts.php` thêm trạng thái khóa, phiên bản phiên đăng nhập, chỉ mục tên đăng nhập duy nhất và chuyển mật khẩu cũ sang dạng băm. Migration đã được áp dụng trên máy hiện tại và `dochoi.sql` đã được cập nhật. Mật khẩu tài khoản cũ vẫn giữ nguyên khi đăng nhập; tài khoản mới cần mật khẩu ít nhất 8 ký tự. Tài khoản bị khóa không đăng nhập được; khóa/mở khóa hoặc đổi mật khẩu sẽ vô hiệu hóa các phiên cũ. Không cho phép tự khóa tài khoản đang đăng nhập.

Migration `20261106_account_roles.php` thêm `role` gồm `admin` (Quản trị viên) và `customer` (Khách hàng), mặc định `customer`. Các tài khoản quản trị có trước migration được giữ vai trò `admin`. Chỉ quản trị viên đang hoạt động được tạo tài khoản có vai trò quản trị hoặc thay đổi vai trò tài khoản khác; không được đổi vai trò tài khoản đang đăng nhập. Đổi vai trò làm mất hiệu lực các phiên đăng nhập cũ. Khách hàng không truy cập được admin.

Phía khách có các đường dẫn `index.php?act=register`, `index.php?act=login` và đăng xuất bằng POST. Đăng ký công khai luôn gán `customer` tại server, không nhận vai trò từ biểu mẫu. Model tài khoản chung nằm tại `Model/TaiKhoan.php`; helper PDO dùng chung tại `Model/pdo.php`.

Migration `20261106_merge_accounts.php` đã sao lưu đầy đủ database trước khi gộp. Bảng `taikhoan` cũ không có bản ghi nên không có tài khoản bị mất hoặc trùng ID; bảng đang dùng được đổi tên từ `admin` sang `taikhoan`, giữ nguyên ID, mật khẩu băm, vai trò và trạng thái. Khóa ngoại `binh_luan.id_taikhoan` và `gio_hang.id_taikhoan` cùng trỏ tới `taikhoan.id`. Chỉ mục duy nhất tên đăng nhập là `taikhoan_username_unique`.

Cài mới chỉ cần import `dochoi.sql` vào database trống. Các migration trước khi gộp bảng được giữ để tham khảo lịch sử; không cần chạy lại trên database mới.
