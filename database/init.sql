SET NAMES utf8mb4;
CREATE TABLE categories (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE posts (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(200) NOT NULL,
  summary VARCHAR(500) NOT NULL,
  content TEXT NOT NULL,
  category_id INT NOT NULL,
  status ENUM('draft','published') NOT NULL DEFAULT 'draft',
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_posts_category FOREIGN KEY(category_id) REFERENCES categories(id) ON DELETE RESTRICT,
  INDEX idx_posts_status_created(status, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
INSERT INTO categories(name) VALUES ('Công nghệ'),('Giáo dục'),('Đời sống'),('Thể thao');
INSERT INTO posts(title,summary,content,category_id,status) VALUES
('Chuyển đổi số trong đời sống','Công nghệ số tiếp tục thay đổi cách học tập, làm việc và tiếp cận dịch vụ.','Chuyển đổi số đang trở thành một phần quan trọng trong đời sống hiện đại. Các nền tảng trực tuyến giúp việc chia sẻ thông tin nhanh hơn và thuận tiện hơn.',1,'published'),
('Những phương pháp học tập hiệu quả','Lập kế hoạch, ôn tập chủ động và nghỉ ngơi hợp lý giúp nâng cao hiệu quả học tập.','Một kế hoạch học tập rõ ràng giúp người học phân bổ thời gian hợp lý. Hãy chia mục tiêu lớn thành các nhiệm vụ nhỏ và thường xuyên tự kiểm tra kiến thức.',2,'published'),
('Sống xanh từ những thói quen nhỏ','Các hành động đơn giản như tiết kiệm điện và giảm nhựa dùng một lần tạo ra khác biệt.','Mỗi người có thể bắt đầu bằng việc mang bình nước cá nhân, phân loại rác và ưu tiên phương tiện công cộng khi phù hợp.',3,'published'),
('Luyện tập thể thao an toàn','Khởi động kỹ, tăng cường độ từ từ và duy trì lịch tập phù hợp là điều cần thiết.','Tập luyện thường xuyên có lợi cho sức khỏe. Người mới bắt đầu nên chọn bài tập vừa sức, uống đủ nước và dành thời gian phục hồi.',4,'published');
