# Hướng dẫn Reset và Seed Database

## Reset toàn bộ database và seed dữ liệu mới

### Bước 1: Reset database
```bash
php artisan migrate:fresh
```

Hoặc nếu muốn giữ migrations và chỉ xóa dữ liệu:
```bash
php artisan db:wipe
php artisan migrate
```

### Bước 2: Chạy tất cả seeders
```bash
php artisan db:seed
```

Hoặc chạy seeder cụ thể:
```bash
php artisan db:seed --class=UserSeeder
php artisan db:seed --class=ClubSeeder
# ... các seeder khác
```

## Thông tin đăng nhập

### Admin
- Email: `admin@gmail.com`
- Password: `123456`

### Member (để test)
- Email: `member@gmail.com`
- Password: `123456`

### Club Managers
- Email: `manager1@gmail.com` đến `manager5@gmail.com`
- Password: `123456`

## Dữ liệu được tạo

### Users
- 1 Admin
- 1 Member cố định (member@gmail.com)
- 5 Club Managers
- 30 Members ngẫu nhiên

### Clubs
- 10 CLB với đầy đủ thông tin

### Club Members
- Mỗi CLB có 1 chủ nhiệm và 5-15 thành viên

### Club Join Requests
- 20 yêu cầu tham gia CLB với các trạng thái:
  - pending
  - scheduling_interview
  - interview (có lịch phỏng vấn)
  - interview_completed (đã phỏng vấn)
  - approved
  - rejected
  - cancelled

### Interview Schedules
- Lịch phỏng vấn cho các requests có trạng thái interview

### Form Questions
- Mỗi CLB có 3-6 câu hỏi form tuyển thành viên

### Form Answers
- Câu trả lời cho các requests đã có câu hỏi

### Posts & Comments
- 5 bài viết
- 20 bình luận

### Events
- 15 sự kiện với các trạng thái khác nhau

