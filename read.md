# DATN Client Features Overview

## 1. Phân quyền & Header Client
- Giữ nguyên giao diện header/sidebar cũ (template `assets1`).  
- Thêm chuông thông báo (dữ liệu lấy từ `users` → `notifications` trong bảng `notifications`).  
- Route `POST /notifications/read` gọi `Client\NotificationController@markAsRead` để update `read_at`.  
- Khó khăn: template gốc dùng nhiều asset `assets/` nên cần đồng bộ sang `assets1` để tương thích build hiện tại.

## 2. Đề xuất sửa thông tin CLB
- View `client/pages/club/edit_request.blade.php` đọc `clubs`, `club_members`, `users`, `faculty_members`, `club_request_updates`, `club_request_member_updates`.  
- Form gửi về `ClubRequestController@store`: ghi vào `club_request_updates` (thông tin CLB mới, file logo) + `club_request_member_updates` (ban quản lý đề xuất).  
- Luồng:
  1. Chủ nhiệm (qua middleware `club_manager`) mở trang để xem trạng thái pending và chi tiết đề xuất trước đó.
  2. Khi submit, dữ liệu lưu ở `club_request_updates` với `status = pending` để admin duyệt (không hề auto approve).  
- Khó khăn: phải giữ đúng nghiệp vụ “chờ admin duyệt” nên chỉ cho phép tạo mới khi không có request pending.

## 3. Form tuyển thành viên & phỏng vấn
- Cấu hình câu hỏi: sử dụng `club_join_form_questions`. Chủ nhiệm tạo câu hỏi (types, options, order, is_required).  
- Câu trả lời lấy từ `club_join_form_answers` (được seed khi member nộp). View `recruit_requests` hiển thị câu trả lời theo từng `club_join_requests`.  
- Hành động duyệt/từ chối chuyển tiếp sang `ClubMemberRequestController` để ghi `club_members`, đảm bảo tái sử dụng logic hiện có.  
- Khó khăn: giữ cho client-side không phá vỡ luồng admin, vì vậy status vẫn `pending` để admin có thể xử lý tiếp nếu cần.

## 4. Tạo & Quản lý bài viết CLB
- `ClubPostController` đọc/ghi bảng `posts`, `media`, `clubs`.  
- View `client/pages/post/new_post` hiển thị form tương tự admin (title, type, visibility, thumbnail, content).  
- Khi lưu:
  - `posts.status` đặt về `pending`.
  - Nếu có file trong nội dung, hàm `processMediaFromContent` quét `<img>/<a>` chứa `/storage/` và liên kết vào bảng `media`.  
- Khó khăn: bản template cũ chỉ có lưới hình statis -> phải thay toàn bộ bằng form tương thích controller sẵn có.

## 5. Gửi thông báo cho thành viên CLB
- Controller mới `Client\ClubNotificationController`:
  - Lấy danh sách thành viên từ `club_members -> members -> users`.
  - Tùy chọn gửi: tất cả, theo chức vụ (`club_members.role`), hoặc chọn cụ thể user.  
- Khi submit, chạy `SendNotificationJob` (job có sẵn) để đẩy email (`sent_emails`) và/hoặc in-app (`notifications`).  
- Dữ liệu không chạm tới bảng admin, chỉ tái sử dụng job + hàng đợi.  
- Khó khăn: cần lọc chính xác thành viên CLB, tránh gửi nhầm user ngoài CLB; đồng thời giữ nguyên luồng queue chung của hệ thống.

## 6. Ghi chú chung
- Sau mỗi thay đổi cấu hình (middleware alias) đều `php artisan optimize:clear` và `config/route/cache:clear`.  
- Các tính năng client luôn đặt `status` ở `pending` hoặc `active` đúng flow để admin (role `admin`) vẫn là nơi duy nhất approve/ reject cuối cùng.  
- Hạn chế: realtime notification chưa bật (chưa có Pusher/Echo), nên chuông chỉ cập nhật khi reload trang.  
- CKEditor/HTML được lưu ở dạng đã sanitize (thẻ cơ bản) và khi gửi mail / thông báo in-app sẽ render đúng định dạng (đậm/ nghiêng, xuống dòng) nhờ `message_html`.

## Khó khăn chính
1. Template client dùng nhiều asset tĩnh dạng `assets/...` → phải đồng bộ sang `assets1` để kết nối với bundle hiện tại (tránh 404).  
2. Logic gửi thông báo/đề xuất/thành viên phụ thuộc vào nhiều bảng quan hệ (`club_members`, `members`, `users`, `club_join_requests`) → phải cẩn thận để không phá vỡ flow admin.  
3. Laravel 12 yêu cầu đăng ký middleware alias trong `bootstrap/app.php`. Sau khi thêm `club_manager`, cần clear cache để tránh `Target class [club_manager] does not exist`.

