# Hướng dẫn sử dụng Docker cho Web4s

## Yêu cầu hệ thống

### 1. Cài đặt Docker Desktop
- Tải và cài đặt Docker Desktop cho Windows từ: https://www.docker.com/products/docker-desktop
- Khởi động Docker Desktop và đảm bảo nó đang chạy

### 2. Kiểm tra cài đặt
```powershell
docker --version
docker-compose --version
```

## Cách sử dụng

### Bước 1: Chuẩn bị source code
1. Copy source code Web4s vào thư mục tương ứng:
   - `configs/web4s-4.0.0/` cho Web4s 4.0.0
   - `configs/web4s-5.0.0/` cho Web4s 5.0.0  
   - `configs/web4s-5.4.0/` cho Web4s 5.4.0

2. Đảm bảo cấu trúc thư mục như sau:
   ```
   configs/web4s-[version]/
   ├── src/                    # Source code CakePHP
   ├── webroot/                # Document root (quan trọng!)
   ├── config/                 # Cấu hình ứng dụng
   └── database/               # SQL scripts (tùy chọn)
   ```

### Bước 2: Khởi động môi trường

#### Cách 1: Sử dụng scripts (Khuyến nghị)
```powershell
# Khởi động Web4s 4.0.0 (PHP 5.6)
.\scripts\start-web4s-4.0.0.ps1

# Khởi động Web4s 5.0.0 (PHP 7.4)
.\scripts\start-web4s-5.0.0.ps1

# Khởi động Web4s 5.4.0 (PHP 8.1)
.\scripts\start-web4s-5.4.0.ps1
```

#### Cách 2: Sử dụng Docker Compose trực tiếp
```powershell
# Web4s 4.0.0
docker-compose -f docker-compose.web4s-4.0.0.yml up -d --build

# Web4s 5.0.0
docker-compose -f docker-compose.web4s-5.0.0.yml up -d --build

# Web4s 5.4.0
docker-compose -f docker-compose.web4s-5.4.0.yml up -d --build
```

### Bước 3: Truy cập ứng dụng

| Version | Website | PhpMyAdmin | Database Port |
|---------|---------|------------|---------------|
| Web4s 4.0.0 | http://localhost:8080 | http://localhost:8081 | 3306 |
| Web4s 5.0.0 | http://localhost:8082 | http://localhost:8083 | 3307 |
| Web4s 5.4.0 | http://localhost:8084 | http://localhost:8085 | 3308 |

### Bước 4: Dừng môi trường
```powershell
# Dừng tất cả
.\scripts\stop-all.ps1

# Hoặc dừng từng version
docker-compose -f docker-compose.web4s-4.0.0.yml down
docker-compose -f docker-compose.web4s-5.0.0.yml down
docker-compose -f docker-compose.web4s-5.4.0.yml down
```

## Cấu hình Database

### Thông tin kết nối
- **Host**: Tên container database (ví dụ: `web4s-4.0.0-db`)
- **Port**: 3306 (trong container)
- **Username**: `web4s_user`
- **Password**: `web4s_password`
- **Database**: `web4s_4_0_0`, `web4s_5_0_0`, `web4s_5_4_0`

### Cấu hình CakePHP
Trong file `config/app.php` hoặc `config/app_local.php`:
```php
'Datasources' => [
    'default' => [
        'host' => $_ENV['DB_HOST'] ?? 'localhost',
        'username' => $_ENV['DB_USER'] ?? 'web4s_user',
        'password' => $_ENV['DB_PASS'] ?? 'web4s_password',
        'database' => $_ENV['DB_NAME'] ?? 'web4s_4_0_0',
        'driver' => 'Cake\Database\Driver\Mysql',
    ],
],
```

## Troubleshooting

### 1. Lỗi port đã được sử dụng
- Kiểm tra xem có ứng dụng nào đang sử dụng port không
- Thay đổi port trong file docker-compose tương ứng

### 2. Lỗi quyền truy cập
```powershell
# Chạy PowerShell với quyền Administrator
# Hoặc thay đổi quyền thư mục
```

### 3. Lỗi build Docker
```powershell
# Xóa image cũ và build lại
docker-compose -f docker-compose.web4s-4.0.0.yml down
docker system prune -f
docker-compose -f docker-compose.web4s-4.0.0.yml up -d --build
```

### 4. Xem logs
```powershell
# Xem logs của web container
docker logs web4s-4.0.0-web

# Xem logs của database
docker logs web4s-4.0.0-db
```

## Lưu ý quan trọng

1. **Chỉ chạy một version tại một thời điểm** để tránh xung đột port
2. **Backup dữ liệu** trước khi thay đổi cấu hình
3. **Kiểm tra cấu trúc thư mục** đảm bảo có thư mục `webroot`
4. **Cập nhật cấu hình database** trong ứng dụng CakePHP
5. **Sử dụng PhpMyAdmin** để quản lý database dễ dàng

## Lợi ích của việc sử dụng Docker

1. **Không cần cài nhiều XAMPP**: Mỗi version PHP chạy độc lập
2. **Dễ dàng chuyển đổi**: Chỉ cần chạy script khác
3. **Môi trường đồng nhất**: Đảm bảo môi trường dev giống production
4. **Quản lý dễ dàng**: Start/stop container thay vì service
5. **Không ảnh hưởng hệ thống**: Môi trường cô lập hoàn toàn 