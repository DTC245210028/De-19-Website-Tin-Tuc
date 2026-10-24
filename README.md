# Đề 19 — Website Tin tức / Cổng thông tin

Stack thực hành: PHP 8.3 + MySQL 8.4 + phpMyAdmin + Nginx reverse proxy + Prometheus + Grafana + Loki + Promtail.

## 1. Yêu cầu
- Docker Desktop hoặc Docker Engine và Docker Compose plugin
- Git
- Cổng 8080, 8443, 8081, 9090, 3000, 3100 chưa bị ứng dụng khác chiếm

## 2. Cấu hình lần đầu
```bash
cp .env.example .env
```
Mở `.env`, đổi toàn bộ mật khẩu trước khi chạy. Tạo chứng chỉ TLS tự ký (cần OpenSSL):
```bash
mkdir -p nginx/certs
openssl req -x509 -nodes -days 365 -newkey rsa:2048 \
  -keyout nginx/certs/server.key -out nginx/certs/server.crt \
  -subj "/CN=localhost"
```
Không commit `.env` hoặc private key lên GitHub.

## 3. Chạy hệ thống
```bash
docker compose up -d --build
docker compose ps
docker compose logs -f --tail=100
```
- Website: https://localhost:8443 (trình duyệt sẽ cảnh báo vì chứng chỉ tự ký)
- HTTP: http://localhost:8080 (chuyển hướng sang HTTPS)
- phpMyAdmin: http://127.0.0.1:8081
- Prometheus: http://127.0.0.1:9090
- Grafana: http://127.0.0.1:3000 — user `admin`, mật khẩu lấy từ `.env`
- Loki API: http://127.0.0.1:3100 (chỉ bind localhost)

Trang quản trị: https://localhost:8443/admin.php. Mật khẩu demo hiện đặt trong mã là `ChangeMe_Admin_2026!`. Đổi ngay trước khi dùng thật. Phiên bản demo dùng mật khẩu quản trị tĩnh để đơn giản hóa bài thực hành; chưa phù hợp cho hệ thống công khai.

## 4. Kiểm tra nhanh
```bash
docker compose ps
docker compose logs nginx
docker compose logs php
docker compose exec db mysql -u news_user -p news_portal
```
Trong MySQL, chạy `SHOW TABLES;` và `SELECT title,status FROM posts;`.
Mở Prometheus > Status > Targets để xem target. Lưu ý: cấu hình Prometheus cơ bản sẵn có; để có metric chuyên sâu của MySQL/container, hãy bổ sung mysqld-exporter và cAdvisor trước khi chụp dashboard hoàn chỉnh. Dashboard mặc định thể hiện `up` và bộ nhớ tiến trình Prometheus; không tự nhận là dashboard đầy đủ metrics container/DB.

## 5. Truy vấn LogQL mẫu
Mở Grafana > Explore > chọn Loki:
```logql
{job="nginx"}
```
```logql
{job="nginx"} |= "GET"
```
```logql
{job="nginx"} |~ " 404 | 500 "
```
Tạo vài request website để sinh log trước khi truy vấn.

## 6. GitHub và 3 commit gợi ý
```bash
git init
git add .
git commit -m "commit 1: deploy news website and database"
git branch -M main
git remote add origin https://github.com/USERNAME/REPOSITORY.git
git push -u origin main
```
Sau đó hoàn thiện reverse proxy và hardening rồi tạo thêm commit 2; thêm cấu hình dashboard/logging và README rồi tạo commit 3. Không dùng `git add .env` hoặc commit private key.

## 7. Giới hạn cần nêu trung thực trong báo cáo
- HTTPS dùng chứng chỉ tự ký phục vụ demo, không phải chứng chỉ tin cậy công khai.
- Trang quản trị là bản minh họa, dùng mật khẩu tĩnh; cần hash mật khẩu, CSRF token, phân quyền và quản lý người dùng trước triển khai thật.
- Để đáp ứng đầy đủ giám sát container/web/DB, cần thêm exporter phù hợp (cAdvisor, mysqld-exporter, nginx-prometheus-exporter) và cấu hình scrape.
- Cần kiểm tra tương thích phiên bản image, log, network và healthcheck trên máy demo. Không công khai các cổng quản trị ra Internet.
